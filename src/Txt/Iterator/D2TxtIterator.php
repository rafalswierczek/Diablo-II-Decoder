<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\Txt\Iterator;

// use rafalswierczek\D2Decoder\Txt\Iterator\{TxtHeader, TxtRow};
// use rafalswierczek\D2Decoder\Txt\{D2TxtDecoder, D2TxtValidator};
// use rafalswierczek\D2Decoder\Txt\Exception\InvalidTxtFileException;

// final class D2TxtIterator implements \Iterator
// {
//     public array $table;
//     private ?array $row;
//     private array $fileHeader;
//     private string $fileName;
//     private array $columnNames;
//     private array $skipColumnValues;
//     private bool $invalidHeader;

//     /**
//      * @throws InvalidTxtFileException
//      * @throws \RuntimeException
//      */
//     public function __construct(
//         array $expectedColumnNames,
//         D2TxtDecoder $d2TxtDecoder,
//         D2TxtValidator $d2TxtValidator,
//         ?SkipColumnsHandler $skipColumnsHandler = null
//     ) {
//         $this->fileName = $d2TxtDecoder->getFileName();
//         $this->expectedColumnNames = $expectedColumnNames;
//         $this->d2TxtValidator = $d2TxtValidator;
//         $this->skipColumnValues = isset($skipColumnsHandler) ? $skipColumnsHandler->getSkipColumnValues() : [];
//     }

//     public function rewind(): void
//     {
//         $this->header = new TxtHeader();
//         $this->header->
//         $this->invalidHeader = false;

//         if (!$this->tableValidator->headerHasDuplicateColumnNames($this->fileHeader, $this->fileName)) {
//             if (!$this->tableValidator->headerHasAllNecessaryColumns($this->fileHeader, $this->columnNames, $this->fileName)) {
//                 $this->invalidHeader = true;
//             }
//         } else {
//             $this->invalidHeader = true;
//         }

//         reset($this->fileLines);

//         $this->next(); // skip header and continue all useless rows until first useful row

//         $this->invalidHeader = false;
//     }

//     public function current(): array
//     {
//         $this->table[] = $this->row;

//         return $this->row;
//     }

//     public function key(): int
//     {
//         return (int) key($this->fileLines) + 1;
//     }

//     public function next(): void
//     {
//         do {
//             next($this->fileLines);
//         } while ($this->continue());
//     }

//     public function valid(): bool
//     {
//         if (null === $this->row) { // end of file OR validation error
//             $this->tableValidator->isEmptyTable($this->table, $this->fileName);

//             return false;
//         }

//         // it's most likely necessary to check valid header each iteration because only valid method can stop the loop in normal way
//         if ($this->invalidHeader || [] === $this->row) { // invalid header OR row has invalid column quantity
//             return false;
//         }

//         return true;
//     }

//     private function continue(): bool
//     {
//         $this->row = $this->getRow();

//         if (null === $this->row) {
//             return false;
//         }
        
//         foreach ($this->skipColumnValues as $columnName => $columnValues) {
//             if (in_array($this->row[$columnName] ?? null, $columnValues)) {
//                 return true;
//             }
//         }

//         return false;
//     }

//     private function getRow(): ?array
//     {
//         if ((false === $current = current($this->fileLines)) || empty($current)) { // end of file
//             return null;
//         }
            
//         $row = array_map('trim', explode("	", $current)); // explode returns always not empty array

//         if ($this->tableValidator->rowHasInvalidColumnQuantity($row, $this->fileHeader, $this->fileName, (key($this->fileLines) + 1))) {
//             return null;
//         }

//         $row = array_combine($this->fileHeader, $row);

//         return $row ?: null; // null in case when header and row are valid and empty
//     }

//     private function getHeader(): array
//     {
//         return [];
//     }
// }
