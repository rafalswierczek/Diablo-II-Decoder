<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\Txt\Iterator;

final class SkipColumnsHandler
{
    private array $columnValues;

    /**
     * @param array $columnValues ['colName1' => ['Expansion', 'some value'], 'colName2' => ['1', '256', '1024']]
     */
    public function __construct(array $columnValues = [])
    {
        $this->columnValues = $columnValues;
    }

    /**
     * Add specific column name and all its values that should make whole row skipped in file iteration process
     * 
     * @throws \RuntimeException
     */
    public function addColumnDataToSkip(string $columnName, string ...$columnValues): void
    {
        if (empty($columnValues)) {
            throw new \RuntimeException('At least one value must be specified');
        }

        $this->columnValues[$columnName] = $columnValues;
    }

    /**
     * @return array ['colName1' => ['Expansion', 'some value'], 'colName2' => ['1', '256', '1024']]
     */
    public function getAllColumnValues(): array
    {
        return $this->columnValues;
    }
}
