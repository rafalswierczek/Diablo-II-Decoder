<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\TestUnit\Txt;

use rafalswierczek\D2Decoder\Txt\D2TxtDecoder;
use rafalswierczek\D2Decoder\Txt\Exception\NotReadableTxtFileException;
use PHPUnit\Framework\TestCase;

class D2TxtDecoderTest extends TestCase
{
    public function testCreateD2TxtDecoderThrowsExceptionWhenInvalidPath()
    {
        $this->expectException(NotReadableTxtFileException::class);
        
        new D2TxtDecoder('');
    }
}
