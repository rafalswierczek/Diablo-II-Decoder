<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\Txt\Iterator;

final class TxtHeader
{
    private array $columnNames;

    public function __construct(array $columnNames, array $expectedColumnNames)
    {
        // validate here

        $this->columnNames = $columnNames;
    }

    public function getColumnNames(): array
    {
        return $this->columnNames;
    }
}
