<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\Txt\Iterator;

use rafalswierczek\D2Decoder\Txt\Exception\{
    DuplicateColumnNameException,
    InvalidRowColumnNumber
};
use rafalswierczek\D2Decoder\Txt\Validation\TxtValidatorInterface;

final class TxtHeader
{
    private array $columnNames;

    /**
     * @param array $columnNames Actual column names from file, ['column1', 'column2', 'column 3']
     * @param array $expectedColumnNames Predefined column names that are expected in valid header, ['column1', 'column2', 'column 3']
     * @param TxtValidatorInterface $txtValidator Validator needed to make sure that TxtHeader object is always valid
     * @throws DuplicateColumnNameException 
     * @throws InvalidRowColumnNumber 
     */
    public function __construct(array $columnNames, array $expectedColumnNames, TxtValidatorInterface $txtValidator)
    {
        $txtValidator->validateHeader($columnNames, $expectedColumnNames);

        $this->columnNames = $columnNames;
    }

    public function getColumnNames(): array
    {
        return $this->columnNames;
    }
}
