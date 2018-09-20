<?php

namespace Tests\Unit\app\Helpers;

use App\Helpers\Arr;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArrTest extends TestCase
{

	public static function dottedArrayProvider()
	{
		return [
			[ ['products.desk.price' => 100], [ 'products' => ['desk' => ['price' => 100] ] ] ],
		];
	}


	/**
	 * @dataProvider dottedArrayProvider
	 */
    public function testArrayUndot($input, $expected)
    {
        $this->assertEquals($expected, Arr::arrayUndot($input));
    }
}
