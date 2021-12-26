<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\Txt;

use rafalswierczek\D2Decoder\D2DecoderInterface;
use rafalswierczek\D2Decoder\Txt\Exception\{
    NotReadableTxtFileException,
    TxtFileCannotBeOpenedException
};

class D2TxtDecoder implements D2DecoderInterface
{
    private $fileHandle;

    /**
     * Opens a file based on the given path
     * 
     * @throws NotReadableTxtFileException
     * @throws TxtFileCannotBeOpenedException
     */
    public function __construct(string $filePath)
    {
        if (!is_readable($filePath)) {
            throw new NotReadableTxtFileException();
        }

        if (false === $this->fileHandle = fopen($filePath, 'rb')) {
            throw new TxtFileCannotBeOpenedException();
        }
    }

    public function decodeRow(): array
    {
        return [];
    }
}
