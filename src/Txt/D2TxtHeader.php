<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\Txt;

class D2TxtHeader
{
    public static function fromFileLine(string $rawHeaderRow): array
    {
        return [];
        // zamiast explode, to użyć niskopoziomowego parsera, który od razu zrobi trim
        //return array_map('trim', explode("	", $this->fileLines[0] ?? ''));
    }
}
