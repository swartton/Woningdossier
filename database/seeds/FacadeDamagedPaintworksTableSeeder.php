<?php

use Illuminate\Database\Seeder;

class FacadeDamagedPaintworksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $items = [
		    [
			    'names' => [
				    'nl' => 'Nee',
			    ],
			    'calculate_value' => 0,
			    'order' => 0,
		    ],
		    [
			    'names' => [
				    'nl' => 'Ja, een beetje',
			    ],
			    'calculate_value' => 3,
			    'order' => 1,
		    ],
		    [
			    'names' => [
				    'nl' => 'Ja, heel erg',
			    ],
			    'calculate_value' => 5,
			    'order' => 2,
		    ],
	    ];

	    foreach($items as $item){
		    $uuid = \App\Helpers\Str::uuid();

		    foreach($item['names'] as $locale => $name){
			    \DB::table('translations')->insert([
				    'key'         => $uuid,
				    'language'    => $locale,
				    'translation' => $name,
			    ]);
		    }

		    \DB::table('facade_damaged_paintworks')->insert([
			    'name' => $uuid,
			    'calculate_value' => $item['calculate_value'],
			    'order' => $item['order'],
		    ]);
	    }
    }
}
