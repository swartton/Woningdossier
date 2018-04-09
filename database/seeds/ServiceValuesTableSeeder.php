<?php

use Illuminate\Database\Seeder;

class ServiceValuesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = [
            [
                'names' => [
                    'nl' => 'Hybride warmtepomp',
                ],
                'service_type' => 'Heating',
                'order' => 0,
                'info' => [
                    'nl' => 'Infotext hier',
                ],
                'service_values' => [
                    [
                        'values' => [
                            'nl' => 'Geen',
                        ],
                        'order' => 1,
                        'calculate_value' => 1,
                    ],
                    [
                        'values' => [
                            'nl' => 'Hybride warmtepomp met buitenlucht als warmtebron',
                        ],
                        'order' => 2,
                        'calculate_value' => 2,
                    ],
                ],
            ],
            [
                'names' => [
                    'nl' => 'Volledige warmtepomp',
                ],
                'service_type' => 'Heating',
                'order' => 0,
                'info' => [
                    'nl' => 'Infotext hier',
                ],
                'service_values' => [
                    [
                        'values' => [
                            'nl' => 'Geen',
                        ],
                        'order' => 1,
                        'calculate_value' => 1,
                    ],
                    [
                        'values' => [
                            'nl' => 'Volledeige warmtepomp met buitenlucht als warmtebron',
                        ],
                        'order' => 2,
                        'calculate_value' => 2,
                    ],
                    [
                        'values' => [
                            'nl' => 'Volledeige warmtepomp met bodemenergie als warmtebron',
                        ],
                        'order' => 3,
                        'calculate_value' => 3,
                    ],
                ],
            ],
            [
                'names' => [
                    'nl' => 'Zonneboiler',
                ],
                'service_type' => 'Heating',
                'order' => 0,
                'info' => [
                    'nl' => 'Infotext hier',
                ],
                'service_values' => [
                    [
                        'values' => [
                            'nl' => 'Geen',
                        ],
                        'order' => 1,
                        'calculate_value' => 1,
                    ],
                    [
                        'values' => [
                            'nl' => 'Voor warm tapwater',
                        ],
                        'order' => 2,
                        'calculate_value' => 2,
                    ],
                    [
                        'values' => [
                            'nl' => 'Voor verwarming',
                        ],
                        'order' => 3,
                        'calculate_value' => 3,
                    ],
                    [
                        'values' => [
                            'nl' => 'Voor verwarming en warm tapwater',
                        ],
                        'order' => 4,
                        'calculate_value' => 4,
                    ],
                ],
            ],
            [
                'names' => [
                    'nl' => 'HR CV ketel',
                ],
                'service_type' => 'Heating',
                'order' => 0,
                'info' => [
                    'nl' => 'Info hier.',
                ],
                'service_values' => [
                    [
                        'values' => [
                            'nl' => 'Aanwezig, recent vervangen',
                        ],
                        'order' => 1,
                        'calculate_value' => 1,
                    ],
                    [
                        'values' => [
                            'nl' => 'Aanwezig, tussen 6 en 13 jaar oud',
                        ],
                        'order' => 2,
                        'calculate_value' => 1,
                    ],
                    [
                        'values' => [
                            'nl' => 'Aanwezig, ouder dan 13 jaar',
                        ],
                        'order' => 3,
                        'calculate_value' => 2,
                    ],
                    [
                        'values' => [
                            'nl' => 'Niet aanwezig',
                        ],
                        'order' => 4,
                        'calculate_value' => 2,
                    ],
                    [
                        'values' => [
                            'nl' => 'Onbekend',
                        ],
                        'order' => 2,
                        'calculate_value' => 5,
                    ],
                ],
            ],
//            [
//                'names' => [
//                    'nl' => 'Douche wtw',
//                ],
//                'service_type' => 'Heating',
//                'order' => 0,
//                'info' => [
//                    'nl' => 'Infotext hier',
//                ],
//                'service_values' => [
//                    [
//                        'values' => [
//                            'nl' => 'Geen',
//                        ],
//                        'order' => 1,
//                        'calculate_value' => 1,
//                    ],
//                    [
//                        'values' => [
//                            'nl' => 'Aanwezig',
//                        ],
//                        'order' => 2,
//                        'calculate_value' => 2,
//                    ],
//                ],
//            ],
            [
                'names' => [
                    'nl' => 'Hoe wordt het huis geventileerd ?',
                ],
                'service_type' => 'Ventilation',
                'order' => 0,
                'info' => [
                    'nl' => 'Infotext hier',
                ],
                'service_values' => [
                    [
                        'values' => [
                            'nl' => 'Natuurlijk',
                        ],
                        'order' => 1,
                        'calculate_value' => 1,
                    ],
                    [
                        'values' => [
                            'nl' => 'Mechanisch',
                        ],
                        'order' => 2,
                        'calculate_value' => 2,
                    ],
                    [
                        'values' => [
                            'nl' => 'Gebalanceerd',
                        ],
                        'order' => 3,
                        'calculate_value' => 3,
                    ],
                    [
                        'values' => [
                            'nl' => 'Decentraal mechanisch',
                        ],
                        'order' => 4,
                        'calculate_value' => 4,
                    ],
                    [
                        'values' => [
                            'nl' => 'Vraaggestuurd',
                        ],
                        'order' => 5,
                        'calculate_value' => 5,
                    ],
                ],
            ],

        ];

        foreach($services as $service){
            $uuid = \App\Helpers\Str::uuid();
            foreach($service['names'] as $locale => $name) {
                \DB::table( 'translations' )->insert( [
                    'key'         => $uuid,
                    'language'    => $locale,
                    'translation' => $name,
                ] );
            }

            $infoUuid = \App\Helpers\Str::uuid();
            foreach($service['info'] as $locale => $name) {
                \DB::table( 'translations' )->insert( [
                    'key'         => $infoUuid,
                    'language'    => $locale,
                    'translation' => $name,
                ] );
            }

            $nameUuid = \DB::table('translations')
                ->where('translation', $service['service_type'])
                ->where('language', 'en')
                ->first(['key']);

            // Get the category. If it doesn't exist: create it
            $serviceType = \DB::table('service_types')->where('name', $nameUuid->key)->first();

            if ($serviceType instanceof \stdClass) {
                $serviceId = \DB::table( 'services' )->insertGetId( [
                    'name'            => $uuid,
                    'service_type_id' => $serviceType->id,
                    'order' => $service['order'],
                    'info' => $infoUuid,
                ] );

                foreach($service['service_values'] as $serviceValue){
                    $uuid = \App\Helpers\Str::uuid();
                    foreach($serviceValue['values'] as $locale => $name){
                        \DB::table( 'translations' )->insert( [
                            'key'         => $uuid,
                            'language'    => $locale,
                            'translation' => $name,
                        ] );
                    }

                    \DB::table('service_values')->insert([
                        'service_id' => $serviceId,
                        'value' => $uuid,
                        'order' => $serviceValue['order'],
                        'calculate_value' => isset($serviceValue['calculate_value']) ? $serviceValue['calculate_value'] : null,
                    ]);
                }
            }
        }
    }
}
