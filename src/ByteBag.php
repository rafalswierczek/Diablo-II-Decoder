<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder;

/**
 * Stores bytes as characters
 */
final class ByteBag
{
    private ByteHandlerInterface $byteHandler;
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
        ByteHandlerInterface $byteHandler,
        int $eolFirstByte,
        int $eolSecondByte,
        int $csvSeparatorByte
    ) {
        $this->byteHandler = $byteHandler;

        $this->eolFirstByte = false !== ($byte = pack('c', $eolFirstByte)) ?
            $byte :
            throw new \RuntimeException('Invalid first byte of end-of-line');

        $this->eolSecondByte = false !== ($byte = pack('c', $eolSecondByte)) ?
            $byte :
            throw new \RuntimeException('Invalid second byte of end-of-line character');

        $this->csvSeparatorByte = false !== ($byte = pack('c', $csvSeparatorByte)) ?
            $byte :
            throw new \RuntimeException('Invalid csv separator byte');
    }

    public function setCurrentByte(int $currentByte): self
    {
        $this->currentByte = false !== ($byte = pack('c', $currentByte)) ?
            $byte :
            throw new \RuntimeException('Invalid current byte');

        return $this;
    }

    /**
     * @throws \RuntimeException 
     */
    public function getCurrentByte(bool $asHexNotation = false): string
    {
        if (null === $this->currentByte) {
            throw new \RuntimeException('You must first set the current byte to get it');
        }

        return $asHexNotation ?
            $this->byteHandler->getHexNotationFromString($this->currentByte) :
            $this->currentByte;
    }

    public function getEndOfLineFirstByte(bool $asHexNotation = false): string
    {
        return $asHexNotation ?
            $this->byteHandler->getHexNotationFromString($this->eolFirstByte) :
            $this->eolFirstByte;
    }

    public function getEndOfLineSecondByte(bool $asHexNotation = false): string
    {
        return $asHexNotation ?
            $this->byteHandler->getHexNotationFromString($this->eolSecondByte) :
            $this->eolSecondByte;
    }

    public function getCsvSeparatorByte(bool $asHexNotation = false): string
    {
        return $asHexNotation ?
            $this->byteHandler->getHexNotationFromString($this->csvSeparatorByte) :
            $this->csvSeparatorByte;
    }
}
