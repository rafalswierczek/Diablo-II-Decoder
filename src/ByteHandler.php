<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder;

/**
 * Manages bytes
 */
final class ByteHandler implements ByteHandlerInterface
{
    /**
     * @throws \RuntimeException
     */
    public function getHexNotationFromInt(int $byte): string
    {
        $stringByte = false !== ($charByte = pack('c', $byte)) ? $charByte : throw new \RuntimeException('Invalid byte as integer type');

        return $this->formatHexNotation($stringByte);
    }

    public function getHexNotationFromString(string $byte): string
    {
        return $this->formatHexNotation($byte);
    }

    private function formatHexNotation(string $character): string
    {
        return '0x' . strtoupper(unpack('H*hex', $character)['hex']);
    }
}
