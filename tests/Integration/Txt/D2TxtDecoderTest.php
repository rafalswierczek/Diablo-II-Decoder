<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\TestIntegration\Txt;

use rafalswierczek\D2Decoder\Txt\D2TxtDecoder;
use PHPUnit\Framework\TestCase;

class D2TxtDecoderTest extends TestCase
{
    private string $filePath;

    protected function setUp(): void
    {
        $this->filePath = dirname(dirname(__DIR__)).'/resources/Weapons.txt';
    }

    public function testDecodeRow()
    {
        $d2TxtDecoder = new D2TxtDecoder($this->filePath);
        
        $this->assertEmpty($d2TxtDecoder->decodeRow());
    }
}
