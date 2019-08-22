<?php

namespace Tests\Unit\app\Calculations;

use App\Calculations\WallInsulation;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Unit\data\ExcelExample;

class WallInsulationTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    public function testCalculate()
    {
        //$this->markTestIncomplete("Work in progress with the input source related traits");
        $user = ExcelExample::user();
        $habits   = ExcelExample::userEnergyHabits();

        $building = ExcelExample::building();

        $habits   = ExcelExample::userEnergyHabits();


        $calculate = [
            'cavity_wall' => 0,
            'element' => [ 3 => 10, ], // 3 = wall insulation element, 10 = geen isolatie
            'insulation_wall_surface' => 33,
            'wall_joints' => 1, // nee
            'contaminated_wall_joints' => 1, // nee
            'facade_plastered_painted' => 2, // nee
        ];

        $results = WallInsulation::calculate($building, $habits, $calculate);

        dd($results);

        /*
          [
              "savings_gas" => 0
              "paint_wall" => array:2 [
                "costs" => 0
                "year" => 0
              ]
              "insulation_advice" => "Spouwmuurisolatie"
              "savings_co2" => 0.0
              "savings_money" => 0.0
              "cost_indication" => 0
              "interest_comparable" => "0,0"
              "repair_joint" => array:2 [
                "costs" => 0
                "year" => 2019
              ]
              "clean_brickwork" => array:2 [
                "costs" => 0
                "year" => 2019
              ]
              "impregnate_wall" => array:2 [
                "costs" => 0
                "year" => 2019
              ]
            ]
         */

    }
}
