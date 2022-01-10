<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder;

interface ByteHandlerInterface
{
    public function getHexNotationFromInt(int $byte): string;

    public function getHexNotationFromString(string $byte): string;
}
