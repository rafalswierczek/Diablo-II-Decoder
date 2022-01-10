<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\TestIntegration\Txt;

use rafalswierczek\D2Decoder\ByteHandler;
use rafalswierczek\D2Decoder\Txt\Exception\{
    InvalidTxtFileException,
    NotReadableTxtFileException
};
use rafalswierczek\D2Decoder\Txt\D2TxtDecoder;
use PHPUnit\Framework\TestCase;

class D2TxtDecoderTest extends TestCase
{
    private string $filePath;

    protected function setUp(): void
    {
        $this->filePath = dirname(dirname(__DIR__)).'/resources/Weapons.txt';
    }

    public function testCreateD2TxtDecoderThrowsExceptionWhenInvalidPath()
    {
        $this->expectException(NotReadableTxtFileException::class);
        
        new D2TxtDecoder('', new ByteHandler());
    }

    public function testDecodeFirstRowAndReturnArrayOfEveryColumn()
    {
        $headerRowData = $this->getHeaderRow();

        $d2TxtDecoder = new D2TxtDecoder($this->filePath, new ByteHandler());
        
        $this->assertEquals($headerRowData['row'], $d2TxtDecoder->decodeRow());
    }

    public function testDecodeRowAndReturnArrayOfEveryColumn()
    {
        $exampleRowData = $this->getExampleRow();

        $d2TxtDecoder = new D2TxtDecoder($this->filePath, new ByteHandler());
        
        $this->assertEquals($exampleRowData['row'], $d2TxtDecoder->decodeRow($exampleRowData['rowNumber']));
    }

    public function testDecodeRowForEmptyFile()
    {
        $this->filePath = dirname(dirname(__DIR__)).'/resources/WeaponsEmpty.txt';

        $rowNumber = 33;

        $this->expectException(InvalidTxtFileException::class);
        $this->expectExceptionMessage(sprintf(
            'Found no enough data to read at row 1 of %d specified rows',
            $rowNumber
        ));

        $d2TxtDecoder = new D2TxtDecoder($this->filePath, new ByteHandler());
        $d2TxtDecoder->decodeRow($rowNumber);
    }

    public function testDecodeRowWhenSeparatorAtTheEndOfLine()
    {
        $this->filePath = dirname(dirname(__DIR__)).'/resources/WeaponsSeparatorAtEol.txt';

        $this->expectException(InvalidTxtFileException::class);
        $this->expectExceptionMessage('File format is invalid because there is no value after separator at row 1');

        $d2TxtDecoder = new D2TxtDecoder($this->filePath, new ByteHandler());
        $d2TxtDecoder->decodeRow();
    }

    private function getHeaderRow(): array
    {
        return ['rowNumber' => 1, 'row' => [
            'name',
            'type',
            'type2',
            'code',
            'alternateGfx',
            'namestr',
            'version',
            'compactsave',
            'rarity',
            'spawnable',
            'mindam',
            'maxdam',
            '1or2handed',
            '2handed',
            '2handmindam',
            '2handmaxdam',
            'minmisdam',
            'maxmisdam',
            '',
            'rangeadder',
            'speed',
            'StrBonus',
            'DexBonus',
            'reqstr',
            'reqdex',
            'durability',
            'nodurability',
            'level',
            'levelreq',
            'cost',
            'gamble cost',
            'magic lvl',
            'auto prefix',
            'OpenBetaGfx',
            'normcode',
            'ubercode',
            'ultracode',
            'wclass',
            '2handedwclass',
            'component',
            'hit class',
            'invwidth',
            'invheight',
            'stackable',
            'minstack',
            'maxstack',
            'spawnstack',
            'flippyfile',
            'invfile',
            'uniqueinvfile',
            'setinvfile',
            'hasinv',
            'gemsockets',
            'gemapplytype',
            'special',
            'useable',
            'dropsound',
            'dropsfxframe',
            'usesound',
            'unique',
            'transparent',
            'transtbl',
            'quivered',
            'lightradius',
            'belt',
            'quest',
            'questdiffcheck',
            'missiletype',
            'durwarning',
            'qntwarning',
            'gemoffset',
            'bitfield1',
            'CharsiMin',
            'CharsiMax',
            'CharsiMagicMin',
            'CharsiMagicMax',
            'CharsiMagicLvl',
            'GheedMin',
            'GheedMax',
            'GheedMagicMin',
            'GheedMagicMax',
            'GheedMagicLvl',
            'AkaraMin',
            'AkaraMax',
            'AkaraMagicMin',
            'AkaraMagicMax',
            'AkaraMagicLvl',
            'FaraMin',
            'FaraMax',
            'FaraMagicMin',
            'FaraMagicMax',
            'FaraMagicLvl',
            'LysanderMin',
            'LysanderMax',
            'LysanderMagicMin',
            'LysanderMagicMax',
            'LysanderMagicLvl',
            'DrognanMin',
            'DrognanMax',
            'DrognanMagicMin',
            'DrognanMagicMax',
            'DrognanMagicLvl',
            'HraltiMin',
            'HraltiMax',
            'HraltiMagicMin',
            'HraltiMagicMax',
            'HratliMagicLvl',
            'AlkorMin',
            'AlkorMax',
            'AlkorMagicMin',
            'AlkorMagicMax',
            'AlkorMagicLvl',
            'OrmusMin',
            'OrmusMax',
            'OrmusMagicMin',
            'OrmusMagicMax',
            'OrmusMagicLvl',
            'ElzixMin',
            'ElzixMax',
            'ElzixMagicMin',
            'ElzixMagicMax',
            'ElzixMagicLvl',
            'AshearaMin',
            'AshearaMax',
            'AshearaMagicMin',
            'AshearaMagicMax',
            'AshearaMagicLvl',
            'CainMin',
            'CainMax',
            'CainMagicMin',
            'CainMagicMax',
            'CainMagicLvl',
            'HalbuMin',
            'HalbuMax',
            'HalbuMagicMin',
            'HalbuMagicMax',
            'HalbuMagicLvl',
            'JamellaMin',
            'JamellaMax',
            'JamellaMagicMin',
            'JamellaMagicMax',
            'JamellaMagicLvl',
            'LarzukMin',
            'LarzukMax',
            'LarzukMagicMin',
            'LarzukMagicMax',
            'LarzukMagicLvl',
            'DrehyaMin',
            'DrehyaMax',
            'DrehyaMagicMin',
            'DrehyaMagicMax',
            'DrehyaMagicLvl',
            'MalahMin',
            'MalahMax',
            'MalahMagicMin',
            'MalahMagicMax',
            'MalahMagicLvl',
            'Source Art',
            'Game Art',
            'Transform',
            'InvTrans',
            'SkipName',
            'NightmareUpgrade',
            'HellUpgrade',
            'Nameable',
            'PermStoreItem'
        ]];
    }

    private function getExampleRow(): array
    {
        return ['rowNumber' => 51, 'row' => [
            'Short Spear',
             'jave',
             '',
             'ssp',
             'jav',
             'ssp',
             '0',
             '',
             '4',
             '1',
             '2',
             '13',
             '',
             '',
             '',
             '',
             '10',
             '22',
             '15',
             '2',
             '10',
             '75',
             '75',
             '40',
             '40',
             '4',
             '',
             '15',
             '0',
             '24',
             '10360',
             '',
             '',
             'jav',
             'ssp',
             '9s9',
             '7s7',
             '1ht',
             '1ht',
             '5',
             '1ht',
             '1',
             '3',
             '1',
             '20',
             '40',
             '40',
             'flpssp',
             'invssp',
             '',
             '',
             '',
             '',
             '0',
             'primarily thrown',
             '0',
             'item_javelins',
             '12',
             'item_javelins',
             '0',
             '0',
             '5',
             '0',
             '0',
             '0',
             '',
             '',
             '1',
             '7',
             '2',
             '0',
             '3',
             '',
             '',
             '',
             '',
             '255',
             '',
             '',
             '1',
             '2',
             '30',
             '',
             '',
             '',
             '',
             '255',
             '1',
             '1',
             '1',
             '1',
             '20',
             '',
             '',
             '',
             '',
             '255',
             '',
             '',
             '',
             '',
             '255',
             '',
             '',
             '',
             '',
             '20',
             '',
             '',
             '',
             '',
             '255',
             '',
             '',
             '',
             '',
             '255',
             '',
             '',
             '',
             '',
             '255',
             '1',
             '1',
             '',
             '',
             '255',
             '1',
             '2',
             '',
             '',
             '255',
             '',
             '',
             '',
             '',
             '255',
             '1',
             '1',
             '',
             '',
             '255',
             '',
             '',
             '',
             '',
             '255',
             '',
             '',
             '1',
             '1',
             '20',
             '1',
             '1',
             '1',
             '1',
             '20',
             '',
             '',
             '5',
             '8',
             '0',
             'tsp',
             'tsp',
             '1',
             '0'
        ]];
    }
}
