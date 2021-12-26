<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder;

use rafalswierczek\D2Decoder\Txt\Exception\NotReadableTxtFileException;

interface D2DecoderInterface
{
    /**
     * @throws NotReadableTxtFileException 
     */
    public function decodeRow(): array;
}
