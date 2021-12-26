<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\Txt;

use rafalswierczek\D2Decoder\Txt\Exception\{
    NotReadableTxtFileException,
    InvalidTxtFileExtensionException,
    TooLargeTxtFileException
};

class D2TxtValidator
{
    private int $maxFileSizeInKiB;

    /**
     * @param int $maxFileSizeInKiB Default is 1 MiB
     */
    public function __construct(int $maxFileSizeInKiB = 1024)
    {
        $this->maxFileSizeInKiB = $maxFileSizeInKiB;
    }

    /**
     * Validate Diablo II excel file
     * 
     * @throws NotReadableTxtFileException
     * @throws InvalidTxtFileExtensionException
     * @throws TooLargeTxtFileException
     */
    public function validateFileMetadata(string $filePath): void
    {
        if (!is_readable($filePath)) {
            throw new NotReadableTxtFileException(sprintf('File "%s" is not readable', $filePath));
        }

        if ('txt' !== pathinfo($filePath, PATHINFO_EXTENSION)) {
            throw new InvalidTxtFileExtensionException(sprintf('File "%s" has invalid extension. Expected .txt', $filePath));
        }

        if ($this->maxFileSizeInKB !== filesize($filePath)) {
            throw new TooLargeTxtFileException(sprintf('File "%s" is too large. Expected maximum of %s', $filePath, $this->formatBytes($this->maxFileSizeInKB * 1024)));
        }
    }

    public function headerHasDuplicateColumnNames(array $header, string $fileName): bool
    {
        if (!empty($duplicateColumnNames = $this->getDuplicateValues($header))) {
            var_dump(['fileName' => $fileName, 'duplicateColumnNames' => implode(', ', $duplicateColumnNames)]); // your notification handler here

            return true;
        }

        return false;
    }

    /**
     * @param array $columnNames Constant defined in table enum
     */
    public function headerHasAllNecessaryColumns(array $header, array $columnNames, string $fileName): bool
    {
        $missingColumnNames = [];
        
        foreach ($columnNames as $columnName) {
            if (!in_array($columnName, $header)) {
                $missingColumnNames[] = $columnName;
            }
        }
        
        if ($missingColumnNames) {
            var_dump(['fileName' => $fileName, 'columnNames' => implode(', ', $missingColumnNames)]); // your notification handler here

            return false;
        }
        
        return true;
    }

    public function rowHasInvalidColumnQuantity(array $row, array $header, string $fileName, int $rowIndex): bool
    {
        if (count($header) !== count($row)) {
            var_dump(['fileName' => $fileName, 'rowIndex' => $rowIndex]); // your notification handler here

            return true;
        }

        return false;
    }

    public function isEmptyTable(array $table, string $fileName): bool
    {
        if (empty($table)) {
            var_dump(['fileName' => $fileName]); // your notification handler here

            return true;
        }

        return false;
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['Byte', 'KiB', 'MiB', 'GiB', 'TiB'];
        $i = 0;
    
        while ($bytes >= 1024) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Returns duplicate values from array that has only one deep level
     * 
     * Typical usage for $oneLevelArray = [0 => 'code', 1 => 'level', 2 => 'dmg', 3 => 'level', 4 => 'speed']
     * 1. array_unique($oneLevelArray) = [0 => 'code', 1 => 'level', 2 => 'dmg', 4 => 'speed']
     * 2. array_diff_assoc(...) = [3 => 'level']
     * 3. array_values(...) = [0 => 'level']
     * array_unique steps for this example with duplicated keys: ['a' => 10, 'b' => 11, 'c' => 13, 'b' => 13]
     * 1. override b=>11 with b=>13 but keep b=>11 index for b=>13: ['a' => 10, 'b' => 13,'c' => 13]
     * 2. ignore c=>13 because it's duplicated value so the final result is: ['a' => 10, 'b' => 13]
     * Next steps:
     * 3. array_diff_assoc(['a' => 10, 'b' => 13, 'c' => 13], ['a' => 10, 'b' => 13]) = ['c' => 13]
     * 4. array_values(...) = [0 => 13]
     * Since last b=>13 element overrides first one and c=>13 element is after the first one, the c=>13 is duplicated value of last b key value whose new place is at first b key
     */
    private function getDuplicateValues(array $oneLevelArray): array
    {
        return array_values(array_diff_assoc($oneLevelArray, array_unique($oneLevelArray)));
    }
}
