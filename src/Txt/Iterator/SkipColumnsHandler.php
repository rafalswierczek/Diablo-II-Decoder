<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\Txt\Iterator;

use rafalswierczek\D2Decoder\Txt\Exception\InvalidValuesOfColumnToSkipException;

final class SkipColumnsHandler
{
    private array $columnValuesData;

    /**
     * @param array $columnValuesData ['colName1' => ['Expansion', 'some value'], 'colName2' => ['1', '256', '1024']]
     * @throws InvalidValuesOfColumnToSkipException 
     */
    public function __construct(array $columnValuesData = [])
    {
        $this->validateColumnValuesData($columnValuesData);

        $this->columnValuesData = $columnValuesData;
    }

    /**
     * Add specific column name and all its values that should make whole row skipped in file iteration process
     * 
     * @throws InvalidValuesOfColumnToSkipException
     */
    public function addColumnDataToSkip(string $columnName, string ...$columnValues): void
    {
        $this->validateAddingColumnValuesData($columnName, $columnValues);

        $this->columnValuesData[$columnName] = $columnValues;
    }

    /**
     * @return array ['colName1' => ['Expansion', 'some value'], 'colName2' => ['1', '256', '1024']]
     * @throws \RuntimeException
     */
    public function getSkipColumnValues(): array
    {
        if (empty($this->columnValuesData)) {
            throw new \RuntimeException('Found empty column values. You must specify it through constructor or addColumnDataToSkip method!');
        }

        return $this->columnValuesData;
    }

    /**
     * @throws InvalidValuesOfColumnToSkipException 
     */
    private function validateColumnValuesData(array $columnValuesData)
    {
        if (!empty($columnValuesData)) {
            foreach ($columnValuesData as $columnName => $columnValues) {
                if (!is_array($columnValues)) {
                    throw new InvalidValuesOfColumnToSkipException(sprintf('Found invalid values to skip type for column name "%s". Expected array of strings', $columnName));
                } elseif (empty($columnValues)) {
                    throw new InvalidValuesOfColumnToSkipException(sprintf('Found no values to skip for column name "%s"', $columnName));
                } else {
                    foreach ($columnValues as $columnValue) {
                        if (empty($columnValue)) {
                            throw new InvalidValuesOfColumnToSkipException(sprintf('Found empty value to skip for column name "%s"', $columnName));
                        } elseif (!is_string($columnValue)) {
                            throw new InvalidValuesOfColumnToSkipException(sprintf('Found invalid type of value to skip for column name "%s". Expected a string', $columnName));
                        }
                    }
                }
            }
        }
    }

    /**
     * @throws InvalidValuesOfColumnToSkipException 
     */
    private function validateAddingColumnValuesData(string $columnName, array $columnValues)
    {
        if (empty($columnValues)) {
            throw new InvalidValuesOfColumnToSkipException(sprintf('Found no values to skip for column name "%s"', $columnName));
        }

        foreach ($columnValues as $columnValue) {
            if (empty($columnValue)) {
                throw new InvalidValuesOfColumnToSkipException(sprintf('Found empty value to skip for column name "%s"', $columnName));
            }
        }
    }
}
