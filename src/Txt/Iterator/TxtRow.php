<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\Txt\Iterator;

use rafalswierczek\D2Decoder\Txt\Iterator\TxtHeader;
use rafalswierczek\D2Decoder\Txt\Validation\TxtValidatorInterface;
use rafalswierczek\D2Decoder\Txt\Exception\InvalidColumnNameException;
use rafalswierczek\D2Decoder\Txt\Exception\InvalidRowColumnNumber;

final class TxtRow
{
    private int $rowNumber;
    private array $rowArray;

    /**
     * 
     * @param array $rowArray ['column1' => 'value1', 'column2' => '2', 'column 3' => 'val 3']
     * @param int $rowNumber Number that represents file line (1 to N)
     * @param TxtHeader $txtHeader Row cannot exist without a header - column names are needed
     * @param TxtValidatorInterface $txtValidator Validator needed to make sure that TxtRow object is always valid
     * @throws InvalidRowColumnNumber 
     */
    public function __construct(array $rowArray, int $rowNumber, TxtHeader $txtHeader, TxtValidatorInterface $txtValidator)
    {
        $txtValidator->validateRow($rowArray, $txtHeader->getColumnNames(), $rowNumber);

        $this->rowArray = $rowArray;
        $this->rowNumber = $rowNumber;
    }

    public function getElement(string $columnName): string
    {
        return $rowArray[$columnName] ?? throw new InvalidColumnNameException(sprintf(
            'Invalid column name "%s" in a row', $columnName
        ));
    }

    /**
     * @return array ['column1' => 'value1', 'column2' => '2', 'column 3' => 'val 3']
     */
    public function getRowArray(): array
    {
        return $this->rowArray;
    }

    public function getRowNumber(): int
    {
        return $this->rowNumber;
    }
}
