<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Appliance.
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingService[] $buildingServices
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\ApplianceProperty[] $properties
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appliance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appliance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appliance whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appliance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Appliance extends Model
{
    public function buildingServices()
    {
        return $this->belongsToMany(BuildingService::class);
    }

    public function properties()
    {
        return $this->hasMany(ApplianceProperty::class);
    }
}
