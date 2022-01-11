<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\Txt\Iterator;

final class TxtRow
{
    private array $rowArray;

    public function getRowArray(): array
    {
        return $this->columnNames;
    }

    public function addElement(string $value, string $columnName): self
    {
        $this->rowArray[$columnName] = $value;
        
        return $this;
    }
}
