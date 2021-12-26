<?php

declare(strict_types=1);

namespace rafalswierczek\D2Decoder\TestUnit\Txt\Iterator;

use PHPUnit\Framework\TestCase;
use rafalswierczek\D2Decoder\Txt\Iterator\SkipColumnsHandler;
use rafalswierczek\D2Decoder\Txt\Exception\InvalidValuesTypeOfColumnToSkipException;

final class SkipColumnsHandlerTest extends TestCase
{
    /**
     * @dataProvider columnValuesProvider
     */
    public function testSkipColumnsHandlerCreation(array $columnValuesData, string $expectedValue)
    {
        $skipColumnsHandler = new SkipColumnsHandler($columnValuesData);

        $columnValue = $skipColumnsHandler->getAllColumnValues()['column_name 2 '][2];

        $this->assertEquals($expectedValue, $columnValue);
    }

    /**
     * @dataProvider columnValuesProvider
     */
    public function testAddingColumnDataToSkip(array $columnValuesData, string $expectedValue)
    {
        $skipColumnsHandler = new SkipColumnsHandler();

        foreach ($columnValuesData as $columnName => $columnValues) {
            $skipColumnsHandler->addColumnDataToSkip($columnName, ...$columnValues);
        }

        $columnValue = $skipColumnsHandler->getAllColumnValues()['column_name 2 '][2];

        $this->assertEquals($expectedValue, $columnValue);
    }

    public function testNotEmptyColumnValuesParameter()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Column values parameter cannot be empty');

        new SkipColumnsHandler([]);
    }

    public function testValuesAreNotEmptyStringsWhenSkipColumnsHandlerIsCreated()
    {
        $columnValuesData = [
            'column name 1' => [""]
        ];

        $this->expectException(InvalidValuesOfColumnToSkipException::class);
        $this->expectExceptionMessage('Found empty values to skip for column name "column name 1"');

        new SkipColumnsHandler($columnValuesData);
    }

    public function testNoValuesWhenSkipColumnsHandlerIsCreated()
    {
        $columnValuesData = [
            'column name 1' => []
        ];

        $this->expectException(InvalidValuesOfColumnToSkipException::class);
        $this->expectExceptionMessage('Found no values to skip for column name "column name 1"');

        new SkipColumnsHandler($columnValuesData);
    }

    public function testNoValuesWhenAddingColumnDataToSkip()
    {
        $columnValuesData = [
            'column name 1' => []
        ];

        $this->expectException(InvalidValuesOfColumnToSkipException::class);
        $this->expectExceptionMessage('Found no values to skip for column name "column name 1"');

        $skipColumnsHandler = new SkipColumnsHandler();

        foreach ($columnValuesData as $columnName => $columnValues) {
            $skipColumnsHandler->addColumnDataToSkip($columnName, ...$columnValues);
        }
    }

    public function testInvalidValuesTypeWhenSkipColumnsHandlerIsCreated()
    {
        $columnValuesData = [
            'column name 1' => "123"
        ];

        $this->expectException(InvalidValuesOfColumnToSkipException::class);
        $this->expectExceptionMessage('Found invalid values to skip type for column name "column name 1". Expected array of strings');

        new SkipColumnsHandler($columnValuesData);
    }

    public function testInvalidValueWhenSkipColumnsHandlerIsCreated()
    {
        $columnValuesData = [
            'column name 1' => ['val1', 2, 'val 3']
        ];

        $this->expectException(InvalidValuesOfColumnToSkipException::class);
        $this->expectExceptionMessage('Found invalid type of value to skip for column name "column name 1". Expected a string');

        new SkipColumnsHandler($columnValuesData);
    }

    public function testInvalidValueWhenAddingColumnDataToSkip()
    {
        $columnValuesData = [
            'column name 1' => ['val1', 2, 'val 3']
        ];

        $this->expectException(InvalidValuesOfColumnToSkipException::class);
        $this->expectExceptionMessage('Found invalid type of value to skip for column name "column name 1". Expected a string');

        $skipColumnsHandler = new SkipColumnsHandler();

        foreach ($columnValuesData as $columnName => $columnValues) {
            $skipColumnsHandler->addColumnDataToSkip($columnName, ...$columnValues);
        }
    }

    public function columnValuesProvider()
    {
        return [
            [
                [
                    'column name 1' => ['value1', 'value 2', '3'],
                    'column_name 2 ' => ['value 1', '', '-1']
                ],
                '-1'
            ]
        ];
    }
}
