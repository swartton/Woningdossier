<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInterest extends Model
{
    //
	public function user(){
		return $this->belongsTo(User::class);
	}

	public function interest(){
		return $this->belongsTo(Interest::class);
	}

	public function getInterestsInServices(){
		$interests = [];
		/*$serviceInterests = $this->where('interested_in_type', 'element')->get();
		foreach($serviceInterests as $serviceInterest){
			$serviceInterest->interested_in_id;
			$element = Service::find($serviceInterest->interested_in_id);
			if ($element instanceof Service){
				$interests[]= $element;
			}
		}*/
		return $interests;
	}

	public function getInterestsInElements(){
		$interests = [];
		$serviceInterests = $this->where('interested_in_type', 'element')->get();
		/** @var self $serviceInterest */
		foreach($serviceInterests as $serviceInterest){
			$serviceInterest->interested_in_id;
			$element = Element::find($serviceInterest->interested_in_id);
			if ($element instanceof Element){
				$interests[]= $element;
			}
		}
		return $interests;
	}

	public function getInterestInMeasureApplications(){
		$interests = [];
		$serviceInterests = $this->where('interested_in_type', 'measure_application')->get();
		/** @var self $serviceInterest */
		foreach($serviceInterests as $serviceInterest){
			$serviceInterest->interested_in_id;
			$element = MeasureApplication::find($serviceInterest->interested_in_id);
			if ($element instanceof MeasureApplication){
				$interests[]= $element;
			}
		}
		return $interests;
	}

	public function getInterests(){
		return [
			'service' => $this->getInterestsInServices(),
			'element' => $this->getInterestsInElements(),
			'measure_application' => $this->getInterestInMeasureApplications(),
		];
	}
}
