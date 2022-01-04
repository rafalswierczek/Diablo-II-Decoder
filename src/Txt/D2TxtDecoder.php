<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\Txt;

use rafalswierczek\D2Decoder\D2DecoderInterface;
use rafalswierczek\D2Decoder\Txt\Exception\{
    NotReadableTxtFileException,
    TxtFileCannotBeOpenedException,
    InvalidEndOfFileException,
    InvalidTxtFileException
};

class D2TxtDecoder implements D2DecoderInterface
{
    const EOL = [0x0D, 0x0A];
    const SEPARATOR = 0x09;

    private $fileHandle;

    /**
     * Opens a file based on the given path
     *
     * @throws NotReadableTxtFileException
     * @throws TxtFileCannotBeOpenedException
     */
    public function __construct(string $filePath)
    {
        if (!is_readable($filePath)) {
            throw new NotReadableTxtFileException();
        }

        if (false === $this->fileHandle = fopen($filePath, 'rb')) {
            throw new TxtFileCannotBeOpenedException();
        }
    }

    /**
     * @param int $rowNumber Value that specifies which row should be parsed started from 1 and ended at n (n is the number of all rows)
     */
    public function decodeRow(int $rowNumber = 1): array
    {
        $rowElement = '';
        $rowArray = [];
        $readRow = true;
        $eolFirstByte = pack('c', self::EOL[0]);
        $eolSecondByte = pack('c', self::EOL[1]);
        $eofLastHexString = '0x' . strtoupper(unpack('H*hex', $eolSecondByte)['hex']);
        $separatorByte = pack('c', self::SEPARATOR);

        $this->movePointerToSpecificRow($rowNumber);

        while ($readRow) {
            $currentByte = fread($this->fileHandle, 1);

            $this->throwIfFileEndsWithoutEofBytes($currentByte);

            if ($eolFirstByte === $currentByte) {
                $this->throwIfPreviousByteIsSeparator($separatorByte, $rowNumber);

                $nextByte = fread($this->fileHandle, 1);

                $this->throwIfFileEndsWithoutSecondEofByte($nextByte, $eofLastHexString);

                $this->throwIfSecondEolByteIsInvalid($eolSecondByte, $nextByte, $eofLastHexString);

                $rowArray[] = $rowElement;

                $readRow = false;
            } else {
                if ($separatorByte === $currentByte) {
                    $rowArray[] = $rowElement;
                    $rowElement = '';
                } else {
                    $rowElement .= $currentByte;
                }
            }
        }

        return $rowArray;
    }

    /**
     * @param string $separatorByte
     * @param int $rowNumber
     * @return void
     * @throws InvalidTxtFileException
     */
    private function throwIfPreviousByteIsSeparator(string $separatorByte, int $rowNumber): void
    {
        fseek($this->fileHandle, -2, SEEK_CUR);

        $previousByte = fread($this->fileHandle, 1);

        if ($separatorByte === $previousByte) {
            throw new InvalidTxtFileException(
                sprintf(
                    'File format is invalid because there is no value (end of line) after separator at row %d',
                    $rowNumber
                )
            );
        }

        fseek($this->fileHandle, 1, SEEK_CUR);
    }

    /**
     * Loop through file <$rowNumber - 1> times so that the file pointer will be on first character in <$rowNumber> row
     *
     * @param int $rowNumber
     * @return void
     * @throws InvalidTxtFileException
     */
    private function movePointerToSpecificRow(int $rowNumber): void
    {
        for ($i = 0; $i < $rowNumber - 1; $i++) {
            if (false === fgets($this->fileHandle)) {
                throw new InvalidTxtFileException(
                    sprintf('Found no enough data to read for %d specified rows', $rowNumber)
                );
            }
        }
    }

    /**
     * @param bool|string $nextByte
     * @param string $eofLastHexString
     * @return void
     * @throws InvalidEndOfFileException
     */
    private function throwIfFileEndsWithoutSecondEofByte(bool|string $nextByte, string $eofLastHexString): void
    {
        if (false === $nextByte) {
            throw new InvalidEndOfFileException(
                sprintf('File ended without second end-of-file character. Expected "%s"', $eofLastHexString)
            );
        }
    }

    /**
     * @param bool|string $eolSecondByte
     * @param bool|string $nextByte
     * @param string $eofLastHexString
     * @return void
     */
    private function throwIfSecondEolByteIsInvalid(
        bool|string $eolSecondByte,
        bool|string $nextByte,
        string $eofLastHexString
    ): void {
        if ($eolSecondByte !== $nextByte) {
            throw new InvalidEndOfFileException(
                sprintf('Invalid end of file character "%s". Expected "%s"', $nextByte, $eofLastHexString)
            );
        }
    }

    /**
     * @param bool|string $currentByte
     * @return void
     */
    private function throwIfFileEndsWithoutEofBytes(bool|string $currentByte): void
    {
        if (false === $currentByte) {
            throw new InvalidTxtFileException('File format is invalid because there is no end of line identifier');
        }
    }
}
