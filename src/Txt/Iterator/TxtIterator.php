<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\Txt\Iterator;

// use rafalswierczek\D2Decoder\Txt\Iterator\{TxtTable, TxtHeader, TxtRow};
// use rafalswierczek\D2Decoder\Txt\Exception\InvalidTxtFileException;
// use rafalswierczek\D2Decoder\Txt\Validation\TxtValidator;
// use rafalswierczek\D2Decoder\Txt\TxtDecoder;

// final class TxtIterator implements \Iterator
// {
//     public TxtTable $table;
//     private TxtRow $row;
//     private TxtHeader $header;
//     private TxtDecoder $decoder;
//     private TxtValidator $validator;
//     private array $expectedColumnNames;
//     private array $skipColumnValues;

//     /**
//      * @throws InvalidTxtFileException
//      * @throws \RuntimeException
//      */
//     public function __construct(
//         array $expectedColumnNames,
//         TxtDecoder $txtDecoder,
//         TxtValidator $txtValidator,
//         ?SkipColumnsHandler $skipColumnsHandler = null
//     ) {
//         $this->expectedColumnNames = $expectedColumnNames;
//         $this->fileName = $txtDecoder->getFileName();
//         $this->decoder = $txtDecoder;
//         $this->validator = $txtValidator;
//         $this->skipColumnValues = isset($skipColumnsHandler) ? $skipColumnsHandler->getSkipColumnValues() : [];
//     }

//     public function rewind(): void
//     {
//         $rowArray = $this->decoder->decodeRow(1);

//         $this->header = new TxtHeader($rowArray, $this->expectedColumnNames, $this->validator);

//         $this->next(); // skip header and continue all useless rows until first useful row
//     }

//     public function current(): TxtRow
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
//             $this->row = $this->decoder->decodeRow();
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
// }
