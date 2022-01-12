<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\Txt\Iterator;

use rafalswierczek\D2Decoder\Txt\Iterator\{TxtHeader, TxtRow};
use rafalswierczek\D2Decoder\Txt\Exception\RowNotFoundException;

final class TxtTable
{
    /**
     * @var TxtRow[]
     */
    private array $rows;
    private TxtHeader $header;
    private int $totalRows = 0;

    public function setHeader(TxtHeader $txtHeader): void
    {
        $this->header = $txtHeader;
        
    }

    public function addRow(TxtRow $txtRow): void
    {
        $this->rows[] = $txtRow;
        $this->totalRows++;
    }

    /**
     * @return TxtRow[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    public function getRow(int $rowNumber): TxtRow
    {
        foreach ($this->rows as $row) {
            if ($rowNumber === $row->getRowNumber()) {
                return $row;
            }       
        }

        throw new RowNotFoundException(sprintf(
            'Cannot find TxtRow object with number %d in TxtTable',
            $rowNumber
        ));
    }

    public function getTotalRows(): int
    {
        return $this->totalRows;
    }
}
