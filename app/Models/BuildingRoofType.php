<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingRoofType extends Model
{
	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	/*protected $casts = [
		'extra' => 'array',
	];*/

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function building(){
		return $this->belongsTo(Building::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function roofType(){
		return $this->belongsTo(RoofType::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function elementValue(){
		return $this->belongsTo(ElementValue::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function insulation(){
		return $this->elementValue();
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function heating(){
		return $this->belongsTo(BuildingHeating::class);
	}

}