<?php

namespace App\Http\Controllers\Cooperation;

use App\Models\Step;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HelpController extends Controller
{
    public function index()
    {
	    // (parts of file => icons)
	    $categorizedFiles = [
		    'algemene_gegevens' => 'general-data',
		    'cv-ketel' => 'high-efficiency-boiler',
		    'dakisolatie' => 'roof-insulation',
		    'gevelisolatie' => 'wall-insulation',
		    'glasisolatie' => 'insulated-glazing',
		    'vloerisolatie' => 'floor-insulation',
		    'zonnepanelen' => 'solar-panels',
			'zonneboiler' => 'heater',
		    'actieplan' => 'my-plan',
		    //
		    'bodemisolatie' => 'floor-insulation',
		    'wtw' => 'ventilation-information',
		    'spouwisolatie' => 'wall-insulation',
		    'kierdichting' => 'insulated-glazing',
		    'ventilatie' => 'ventilation-information',
		    'warmtepomp' => 'heat-pump',
	    ];

	    // allowed extensions / files
	    $allowedExtensions = ['pdf'];
	    $start = 'Invul_hulp_';

	    // Create a new collection for the files
	    //$files = collect();
	    $files = [];

	    $steps = Step::orderBy('order')->pluck('slug');
	    $steps = array_flip($steps->toArray());

	    // Go through the dir and get the filepath
	    foreach (\Storage::files('public/hoomdossier-assets') as $filePath) {

		    // get the extenstion of the file
		    $fileExtension = pathinfo($filePath)['extension'];
		    // don't display the Info- prefixed files. Only measures.
		    if (in_array($fileExtension, $allowedExtensions) && stristr($filePath, $start) !== false) {

			    // Change public to storage
			    $file = str_replace('public', 'storage', $filePath);
			    foreach($categorizedFiles as $part => $categoryImage){
				    if (stristr($filePath, $part) !== false){
					    $image = $categoryImage;
				    }
			    }
			    if(!array_key_exists($image, $files)){
				    $files[$image] = [];
			    }
			    $files[$image][]= $file;

			    // push them inside a collection
			    //$files->push($fileData);
		    }
	    }

	    uksort($files, function($fileA, $fileB) use($steps){
		    $indexA = 99;
		    $indexB = 99;
		    if(array_key_exists($fileA, $steps)){
			    $indexA = $steps[$fileA];
		    }
		    if (array_key_exists($fileB, $steps)){
			    $indexB = $steps[$fileB];
		    }
		    if ($indexA == $indexB){
			    return 0;
		    }
		    if ($indexA < $indexB){
			    return -1;
		    }
		    return 1;
	    });


        return view('cooperation.help.index', compact('files'));
    }
}