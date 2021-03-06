<?php

namespace Tests\Unit\app\Helpers;

use App\Helpers\Str;
use Tests\TestCase;

class StrTest extends TestCase
{
    public static function isConsideredEmptyAnswerProvider()
    {
        return [
            [null], ['null'], ['0'],
            ['0.00'], ['0.0'],
        ];
    }

    /**
     * @dataProvider isConsideredEmptyAnswerProvider
     */
    public function testisConsideredEmptyAnswer($values)
    {
        $this->assertEquals(true, Str::isConsideredEmptyAnswer($values));
    }
}
