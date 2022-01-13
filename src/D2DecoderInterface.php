<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder;

interface D2DecoderInterface
{
    public function decodeRow(): array;
}
