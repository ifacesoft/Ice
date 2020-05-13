<?php
namespace Ice\Helper;

use PHPUnit\Framework\TestCase;

class ArraysTest extends TestCase
{
    public function testColumns()
    {
        $array = [
            'first' => [
                'one' => 1,
                'two' => 2,
                'three' => 3,
                4 => 4
            ],
            'second' => [
                'one' => 11,
                'two' => 22,
                'three' => 33,
                4 => 44
            ],
            'third' => [
                'one' => 111,
                'two' => 222,
                'three' => 333,
                4 => 444
            ]
        ];

        $this->assertEquals(Type_Array::column($array, 'one'), ['first' => 1, 'second' => 11, 'third' => 111]);
        $this->assertEquals(Type_Array::column($array, 'one', 'three'), [3 => 1, 33 => 11, 333 => 111]);
        $this->assertEquals(Type_Array::column($array, 'one', ''), [0 => 1, 1 => 11, 2 => 111]);
        $this->assertEquals(Type_Array::column($array, 'one', 4), [4 => 1, 44 => 11, 444 => 111]);
        $this->assertEquals(Type_Array::column($array, 'one', 4), [4 => 1, 44 => 11, 444 => 111]);
        $this->assertEquals(Type_Array::column($array, null, 'two'), [
            2 => [
                'one' => 1,
                'two' => 2,
                'three' => 3,
                4 => 4
            ],
            22 => [
                'one' => 11,
                'two' => 22,
                'three' => 33,
                4 => 44
            ],
            222 => [
                'one' => 111,
                'two' => 222,
                'three' => 333,
                4 => 444
            ]
        ]);
        $this->assertEquals(Type_Array::column($array, 0, 'one'), [1 => 1, 11 => 11, 111 => 111]);
        $this->assertEquals(Type_Array::column($array, ['one', 'two'], 'one'), [1 => '1__2', 11 => '11__22', 111 => '111__222']);
    }

    public function testPassingByReference()
    {
        $a = 2;
        $b = &$a;
        $b = 5;
        $this->assertEquals($a, 5);
    }
}