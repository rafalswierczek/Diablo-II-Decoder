<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\Txt;

use rafalswierczek\D2Decoder\D2DecoderInterface;
use rafalswierczek\D2Decoder\Txt\Exception\{
    NotReadableTxtFileException,
    TxtFileCannotBeOpenedException,
    InvalidEndOfLineException,
    InvalidTxtFileException
};

class D2TxtDecoder implements D2DecoderInterface
{
    const EOL = [0x0D, 0x0A];
    const SEPARATOR = 0x09;

    private $fileHandle;

    /**
     * Open a file based on the given path
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
     * Read specific row and return it as array and move pointer to the next row
     * 
     * @param int $rowNumber Value that specifies which row should be parsed started from 1 and ended at n (n is the number of all rows)
     * @return array 
     * @throws \RuntimeException 
     * @throws InvalidTxtFileException 
     * @throws InvalidEndOfLineException 
     */
    public function decodeRow(int $rowNumber = 1): array
    {
        $rowElement = '';
        $rowArray = [];
        $byteBag = new ByteBag(self::EOL[0], self::EOL[1], self::SEPARATOR);

        $this->throwIfRowNumberIsZeroOrLess($rowNumber);

        $this->movePointerToSpecificRow($rowNumber);
        
        while (true) {
            $currentChar = fread($this->fileHandle, 1);
            
            $this->throwIfThereAreNoBytesLeft($currentChar, $rowNumber);

            $byteBag->setCurrentByte(hexdec(bin2hex($currentChar)));
            
            if (!$this->isPointerAtTheEndOfLine($rowNumber, $byteBag)) {
                if ($byteBag->getCsvSeparatorByte() !== $byteBag->getCurrentByte()) {
                    $rowElement .= $byteBag->getCurrentByte();
                } else {
                    $rowArray[] = $rowElement;
                    $rowElement = '';
                }
            } else {
                $rowArray[] = $rowElement;
                fseek($this->fileHandle, 2, SEEK_CUR); // move to the next row
                break;
            }
        }

        return $rowArray;
    }

    /**
     * @param int $rowNumber 
     * @param ByteBag $byteBag 
     * @return bool 
     * @throws \RuntimeException 
     * @throws InvalidTxtFileException 
     * @throws InvalidEndOfLineException 
     */
    public function isPointerAtTheEndOfLine(int $rowNumber, ByteBag $byteBag): bool
    {
        if ($byteBag->getEndOfLineFirstByte() === $byteBag->getCurrentByte()) {
            $this->throwIfPreviousByteIsSeparator($byteBag->getCsvSeparatorByte(), $rowNumber);

            $nextByte = fread($this->fileHandle, 1);

            $this->throwIfSecondEolByteIsInvalid(
                $nextByte,
                $byteBag->getEndOfLineSecondByte(),
                $byteBag->getEndOfLineSecondByteHexNotation(),
                $rowNumber
            );

            fseek($this->fileHandle, -1, SEEK_CUR);

            return true;
        }

        return false;
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
                throw new InvalidTxtFileException(sprintf(
                    'Found no enough data to read at row %d of %d specified rows',
                    $i + 1,
                    $rowNumber
                ));
            }
        }
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
            throw new InvalidTxtFileException(sprintf(
                'File format is invalid because there is no value after separator at row %d',
                $rowNumber
            ));
        }

        fseek($this->fileHandle, 1, SEEK_CUR);
    }

    /**
     * @param bool|string $byteAfterFirstEolByte 
     * @param string $eolSecondByte 
     * @param string $expectedEolSecondByteHexNotation
     * @param int $rowNumber 
     * @return void 
     * @throws InvalidEndOfLineException 
     */
    private function throwIfSecondEolByteIsInvalid(
        bool|string $byteAfterFirstEolByte,
        string $eolSecondByte,
        string $expectedEolSecondByteHexNotation,
        int $rowNumber
    ): void {
        if (false === $byteAfterFirstEolByte) {
            throw new InvalidEndOfLineException(sprintf(
                'Row $d ended without second end-of-line byte. Expected "%s"',
                $rowNumber,
                $expectedEolSecondByteHexNotation
            ));
        }

        if ($eolSecondByte !== $byteAfterFirstEolByte) {
            throw new InvalidEndOfLineException(sprintf(
                'Invalid end-of-line byte "%s at row %d". Expected "%s"',
                $byteAfterFirstEolByte,
                $rowNumber,
                $expectedEolSecondByteHexNotation
            ));
        }
    }

    /**
     * @param bool|string $currentChar 
     * @param int $rowNumber 
     * @return void 
     * @throws InvalidTxtFileException 
     */
    private function throwIfThereAreNoBytesLeft(bool|string $currentChar, int $rowNumber): void
    {
        if (false === $currentChar) {
            throw new InvalidTxtFileException(sprintf(
                'File format is invalid because there is no end of line identifier at row %d',
                $rowNumber
            ));
        }
    }

    /**
     * @param int $rowNumber 
     * @return void 
     * @throws \RuntimeException 
     */
    private function throwIfRowNumberIsZeroOrLess(int $rowNumber): void
    {
        if (0 >= $rowNumber) {
            throw new \RuntimeException('Row number must be greater than 0');
        }
    }
}
