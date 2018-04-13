<?php

use Illuminate\Database\Seeder;

class PvPanelYieldsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$items = [
    	    'n-o' => [
    	    	10 => 0.81,
		        15 => 0.78,
		        20 => 0.74,
				30 => 0.68,
		        40 => 0.61,
		        45 => 0.58,
		        50 => 0.54,
		        60 => 0.48,
		        70 => 0.42,
		        75 => 0.38,
	            90 => 0.29,
	        ],
	        'o-n-o' => [
		        10 => 0.83,
		        15 => 0.82,
		        20 => 0.79,
		        30 => 0.75,
		        40 => 0.70,
		        45 => 0.67,
		        50 => 0.65,
		        60 => 0.59,
		        70 => 0.54,
		        75 => 0.51,
		        90 => 0.39,
	        ],
	        'o' => [
		        10 => 0.85,
		        15 => 0.86,
		        20 => 0.83,
		        30 => 0.82,
		        40 => 0.79,
		        45 => 0.77,
		        50 => 0.75,
		        60 => 0.70,
		        70 => 0.65,
		        75 => 0.63,
		        90 => 0.48,
	        ],
	        'o-z-o' => [
		        10 => 0.87,
		        15 => 0.89,
		        20 => 0.89,
		        30 => 0.89,
		        40 => 0.86,
		        45 => 0.85,
		        50 => 0.83,
		        60 => 0.78,
		        70 => 0.73,
		        75 => 0.70,
		        90 => 0.52,
	        ],
	        'z-o' => [
		        10 => 0.90,
		        15 => 0.93,
		        20 => 0.96,
		        30 => 0.95,
		        40 => 0.94,
		        45 => 0.93,
		        50 => 0.91,
		        60 => 0.87,
		        70 => 0.80,
		        75 => 0.77,
		        90 => 0.64,
	        ],
	        'z-z-o' => [
		        10 => 0.93,
		        15 => 0.94,
		        20 => 0.96,
		        30 => 0.98,
		        40 => 0.97,
		        45 => 0.96,
		        50 => 0.93,
		        60 => 0.90,
		        70 => 0.84,
		        75 => 0.81,
		        90 => 0.67,
	        ],
	        'z' => [
		        10 => 0.95,
		        15 => 0.96,
		        20 => 0.98,
		        30 => 1.00,
		        40 => 1.00,
		        45 => 0.99,
		        50 => 0.96,
		        60 => 0.94,
		        70 => 0.87,
		        75 => 0.84,
		        90 => 0.69,
	        ],
	        'z-z-w' => [
		        10 => 0.93,
		        15 => 0.94,
		        20 => 0.96,
		        30 => 0.98,
		        40 => 0.97,
		        45 => 0.96,
		        50 => 0.93,
		        60 => 0.90,
		        70 => 0.84,
		        75 => 0.81,
		        90 => 0.67,
	        ],
	        'z-w' => [
		        10 => 0.90,
		        15 => 0.93,
		        20 => 0.96,
		        30 => 0.95,
		        40 => 0.94,
		        45 => 0.93,
		        50 => 0.91,
		        60 => 0.87,
		        70 => 0.80,
		        75 => 0.77,
		        90 => 0.69,
	        ],
	        'w-z-w' => [
		        10 => 0.87,
		        15 => 0.89,
		        20 => 0.89,
		        30 => 0.89,
		        40 => 0.86,
		        45 => 0.85,
		        50 => 0.83,
		        60 => 0.78,
		        70 => 0.73,
		        75 => 0.70,
		        90 => 0.52,
	        ],
	        'w' => [
		        10 => 0.85,
		        15 => 0.86,
		        20 => 0.83,
		        30 => 0.82,
		        40 => 0.79,
		        45 => 0.77,
		        50 => 0.75,
		        60 => 0.70,
		        70 => 0.65,
		        75 => 0.63,
		        90 => 0.48,
	        ],
	        'w-n-w' => [
		        10 => 0.83,
		        15 => 0.82,
		        20 => 0.79,
		        30 => 0.75,
		        40 => 0.70,
		        45 => 0.67,
		        50 => 0.65,
		        60 => 0.59,
		        70 => 0.54,
		        75 => 0.51,
		        90 => 0.39,
	        ],
	        'n-w' => [
		        10 => 0.81,
		        15 => 0.78,
		        20 => 0.74,
		        30 => 0.68,
		        40 => 0.61,
		        45 => 0.58,
		        50 => 0.55,
		        60 => 0.48,
		        70 => 0.42,
		        75 => 0.38,
		        90 => 0.29,
	        ],
	    ];

        foreach($items as $short => $item){
        	$orientation = \DB::table('pv_panel_orientations')->where('short', $short)->first();

        	foreach($item as $angle => $yield) {
		        \DB::table( 'pv_panel_yields' )->insert( [
			        'angle'                   => $angle,
			        'pv_panel_orientation_id' => $orientation->id,
			        'yield'                   => $yield,
		        ] );
	        }
        }
    }
}
