<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingServiceValue
 *
 * @property-read \App\Models\BuildingService $buildingService
 * @mixin \Eloquent
 * @property int $id
 * @property int|null $building_service_id
 * @property string $name
 * @property string $value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingServiceValue whereBuildingServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingServiceValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingServiceValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingServiceValue whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingServiceValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingServiceValue whereValue($value)
 */
class BuildingServiceValue extends Model
{
    public function buildingService(){
    	return $this->belongsTo(BuildingService::class);
    }
}
