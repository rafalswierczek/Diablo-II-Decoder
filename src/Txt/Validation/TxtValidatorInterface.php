<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\Txt\Validation;

use rafalswierczek\D2Decoder\Txt\Exception\{
    NotReadableTxtFileException,
    InvalidTxtFileExtensionException,
    TooLargeTxtFileException,
    DuplicateColumnNameException,
    InvalidRowColumnNumber
};

interface TxtValidatorInterface
{
    /**
     * Validate Diablo II excel file
     * 
     * @throws NotReadableTxtFileException
     * @throws InvalidTxtFileExtensionException
     * @throws TooLargeTxtFileException
     */
    public function validateFileMetadata(string $filePath): void;

    /**
     * @throws DuplicateColumnNameException
     * @throws InvalidRowColumnNumber
     */
    public function validateHeader(array $columnNames, array $expectedColumnNames): void;

    /**
     * @throws InvalidRowColumnNumber
     */
    public function validateRow(array $rowArray, array $headerColumnNames, int $rowNumber): void;
}
