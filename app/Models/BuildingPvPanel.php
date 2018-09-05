<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingPvPanel
 *
 * @property int $id
 * @property int $building_id
 * @property int|null $peak_power
 * @property int $number
 * @property int $pv_panel_orientation_id
 * @property int|null $angle
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\PvPanelOrientation $orientation
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel whereAngle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel wherePeakPower($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel wherePvPanelOrientationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPvPanel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingPvPanel extends Model
{

    protected $fillable = ['building_id', 'peak_power', 'number', 'pv_panel_orientation_id', 'angle'];
	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function building(){
		return $this->belongsTo(Building::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function orientation(){
		return $this->belongsTo(PvPanelOrientation::class);
	}
}
