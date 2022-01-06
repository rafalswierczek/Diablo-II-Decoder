<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\Txt;

use RuntimeException;

/**
 * Stores bytes as characters
 */
class ByteBag
{
    private string $currentByte;
    private string $eolFirstByte;
    private string $eolSecondByte;
    private string $csvSeparatorByte;

    /**
     * Takes bytes as parameters from decimal notation
     * 
     * @throws \RuntimeException 
     */
    public function __construct(
        int $eolFirstByte,
        int $eolSecondByte,
        int $csvSeparatorByte
    ) {
        $this->eolFirstByte = false !== ($byte = pack('c', $eolFirstByte)) ? $byte : throw new \RuntimeException('Invalid first byte of end-of-line');
        $this->eolSecondByte = false !== ($byte = pack('c', $eolSecondByte)) ? $byte : throw new \RuntimeException('Invalid second byte of end-of-line character');
        $this->csvSeparatorByte = false !== ($byte = pack('c', $csvSeparatorByte)) ? $byte : throw new \RuntimeException('Invalid csv separator byte');
    }

    public function setCurrentByte(int $currentByte): self
    {
        $this->currentByte = false !== ($byte = pack('c', $currentByte)) ? $byte : throw new \RuntimeException('Invalid current byte');

        return $this;
    }

    /**
     * @throws RuntimeException 
     */
    public function getCurrentByte(): string
    {
        if (null === $this->currentByte) {
            throw new \RuntimeException('You must first set the current byte to get it');
        }

        return $this->currentByte;
    }

    public function getEndOfLineFirstByte(): string
    {
        return $this->eolFirstByte;
    }

    public function getEndOfLineSecondByte(): string
    {
        return $this->eolSecondByte;
    }

    public function getCsvSeparatorByte(): string
    {
        return $this->csvSeparatorByte;
    }

    public function getEndOfLineSecondByteHexNotation(): string
    {
        return '0x' . strtoupper(unpack('H*hex', $this->eolSecondByte)['hex']);
    }
}
