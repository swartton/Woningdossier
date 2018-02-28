<?php

namespace Tests\Unit\app\Rules;

use App\Rules\PhoneNumber;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhoneNumberTest extends TestCase
{

	public static function phoneNumberProvider(){
		return [
			[ 'nl', '+31612345678', true, ],
			[ 'nl', '+31(0)612345678', true, ],
			[ 'nl', '(+31)(0)612345678', true, ],
			[ 'nl', '0612345678', true, ],
			[ 'nl', '06-12345678', true, ],
			[ 'nl', '+316-12345678', true, ],
			[ 'nl', '0031612345678', true, ],
			[ 'nl', '00310612345678', true, ],
			[ 'nl', '(0031)0612345678', false, ],
			[ 'nl', 'fdkslfasjl', false, ],
			[ 'nl', '59204585920', false, ],
			[ 'nl', '1612345678', false, ],
		];
	}

	/**
	 * @dataProvider phoneNumberProvider
	 */
	public function testPasses($country, $phoneNumber, $shouldPass){
		$phoneNumberRule = new PhoneNumber($country);
		$this->assertEquals($shouldPass, $phoneNumberRule->passes('phone_number', $phoneNumber));
	}
}
