<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\Txt;

use rafalswierczek\D2Decoder\Txt\Validation\TxtValidatorInterface;
use rafalswierczek\D2Decoder\{ByteBag, ByteHandlerInterface};
use rafalswierczek\D2Decoder\D2DecoderInterface;
use rafalswierczek\D2Decoder\Txt\Exception\{
    NotReadableTxtFileException,
    TxtFileCannotBeOpenedException,
    InvalidEndOfLineException,
    InvalidTxtFileException,
    InvalidTxtFileExtensionException,
    NoDataToReadException,
    TooLargeTxtFileException
};

class TxtDecoder implements D2DecoderInterface
{
    const EOL = ['CR' => 0x0D, 'LF' => 0x0A];
    const SEPARATOR = 0x09;

    private int $rowNumber = 1;
    private $fileHandle;
    private string $fileName;

    /**
     * Open a file based on the given path
     * 
     * @param string $filePath 
     * @param ByteHandlerInterface $byteHandler 
     * @param TxtValidatorInterface $txtValidator 
     * @return void 
     * @throws NotReadableTxtFileException 
     * @throws InvalidTxtFileExtensionException 
     * @throws TooLargeTxtFileException 
     * @throws TxtFileCannotBeOpenedException 
     */
    public function __construct(string $filePath, ByteHandlerInterface $byteHandler, TxtValidatorInterface $txtValidator)
    {
        $txtValidator->validateFileMetadata($filePath);

        if (false === $this->fileHandle = fopen($filePath, 'rb')) {
            throw new TxtFileCannotBeOpenedException();
        }

        $this->byteHandler = $byteHandler;
        $this->fileName = basename($filePath);
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getRowNumber(): int
    {
        return $this->rowNumber;
    }

    /**
     * Read specific row and return it as array and move pointer to the next row
     * 
     * @param int $rowNumber Value that specifies which row should be parsed started from 1 and ended at n (n is the number of all rows).
     *  If $rowNumber is specified then next calls of decodeRow will result in an increment of specified $rowNumber.
     *  If $rowNumber is not specified then next calls of decodeRow will result in an increment of default $rowNumber which is 1. 
     * @return array Return null if it's end of file
     * @throws \RuntimeException 
     * @throws InvalidTxtFileException 
     * @throws InvalidEndOfLineException 
     * @throws NoDataToReadException
     */
    public function decodeRow(?int $rowNumber = null): array
    {
        $rowElement = '';
        $rowArray = [];
        $byteBag = new ByteBag($this->byteHandler, self::EOL['CR'], self::EOL['LF'], self::SEPARATOR);

        if (null !== $rowNumber) {
            $this->rowNumber = $rowNumber; // if there is a need for specific row number then overwrite local one
            $this->throwIfRowNumberIsInvalid();
            $this->movePointerToSpecificRow();
        }

        $this->throwIfThereAreNoBytes();

        while (true) {
            $currentChar = fread($this->fileHandle, 1);

            if (false === $currentChar) { // end of file
                $rowArray[] = $rowElement;
                break;
            }
            
            $byteBag->setCurrentByte(hexdec(bin2hex($currentChar)));
            
            if ($this->isPointerAtTheEndOfLine($byteBag)) {
                $rowArray[] = $rowElement;
                break;
            } else {
                $currentByte = $byteBag->getCurrentByte();

                if ($byteBag->getCsvSeparatorByte() !== $currentByte) {
                    $rowElement .= $currentByte;
                } else {
                    $rowArray[] = $rowElement;
                    $rowElement = '';
                }
            }
        }

        $this->rowNumber++;

        return $rowArray;
    }

    /**
     * @param ByteBag $byteBag 
     * @return bool 
     * @throws \RuntimeException 
     * @throws InvalidTxtFileException 
     * @throws InvalidEndOfLineException 
     */
    private function isPointerAtTheEndOfLine(ByteBag $byteBag): bool
    {
        $currentByte = $byteBag->getCurrentByte();

        if (
            $byteBag->getEndOfLineFirstByte() === $currentByte ||
            $byteBag->getEndOfLineSecondByte() === $currentByte
        ) {
            $this->throwIfPreviousByteIsSeparator($byteBag->getCsvSeparatorByte(), $this->rowNumber);

            if ($byteBag->getEndOfLineFirstByte() === $currentByte) {
                $nextByte = fread($this->fileHandle, 1);
    
                $this->throwIfSecondEolByteIsInvalid(
                    $nextByte,
                    $byteBag->getEndOfLineSecondByte(),
                    $this->rowNumber
                );
            }

            return true;
        }

        return false;
    }

    /**
     * Loop through file <$rowNumber - 1> times so that the file pointer will be on first character in <$rowNumber> row
     *
     * @throws NoDataToReadException
     */
    private function movePointerToSpecificRow(): void
    {
        for ($i = 0; $i < $this->rowNumber - 1; $i++) {
            if (false === fgets($this->fileHandle)) {
                throw new NoDataToReadException(sprintf(
                    'Found no enough data to read at row %d of %d specified rows',
                    $i + 1,
                    $this->rowNumber
                ));
            }
        }
    }

    /**
     * @param string $separatorByte
     * @return void
     * @throws InvalidTxtFileException
     */
    private function throwIfPreviousByteIsSeparator(string $separatorByte): void
    {
        fseek($this->fileHandle, -2, SEEK_CUR);

        $previousByte = fread($this->fileHandle, 1);

        if ($separatorByte === $previousByte) {
            throw new InvalidTxtFileException(sprintf(
                'File format is invalid because there is no value after separator at row %d',
                $this->rowNumber
            ));
        }

        fseek($this->fileHandle, 1, SEEK_CUR);
    }

    /**
     * @param bool|string $byteAfterFirstEolByte 
     * @param string $eolSecondByte 
     * @param string $expectedEolSecondByteHexNotation
     * @return void 
     * @throws InvalidEndOfLineException 
     */
    private function throwIfSecondEolByteIsInvalid(
        bool|string $byteAfterFirstEolByte,
        string $eolSecondByte
    ): void {
        if (false === $byteAfterFirstEolByte) {
            throw new InvalidEndOfLineException(sprintf(
                'Row $d ended without second end-of-line byte. Expected "%s"',
                $this->rowNumber,
                $this->byteHandler->getHexNotationFromString($eolSecondByte)
            ));
        }

        if ($eolSecondByte !== $byteAfterFirstEolByte) {
            throw new InvalidEndOfLineException(sprintf(
                'Invalid end-of-line byte "%s" at row %d. Expected "%s"',
                $this->byteHandler->getHexNotationFromString($byteAfterFirstEolByte),
                $this->rowNumber,
                $this->byteHandler->getHexNotationFromString($eolSecondByte)
            ));
        }
    }

    /**
     * @throws NoDataToReadException 
     */
    private function throwIfThereAreNoBytes(): void
    {
        $currentChar = fread($this->fileHandle, 1);
        fseek($this->fileHandle, -1, SEEK_CUR);

        if (empty($currentChar)) {
            throw new NoDataToReadException(sprintf(
                'There is no data to read in txt file at row %d',
                $this->rowNumber
            ));
        }
    }

    /**
     * @throws \RuntimeException 
     */
    private function throwIfRowNumberIsInvalid(): void
    {
        if (0 >= $this->rowNumber) {
            throw new \RuntimeException('Row number must be greater than 0');
        }
    }
}
