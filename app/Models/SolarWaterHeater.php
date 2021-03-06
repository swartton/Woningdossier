<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SolarWaterHeater
 *
 * @property int $id
 * @property string $name
 * @property int $calculate_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SolarWaterHeater newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SolarWaterHeater newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SolarWaterHeater query()
 * @method static \Illuminate\Database\Eloquent\Builder|SolarWaterHeater whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SolarWaterHeater whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SolarWaterHeater whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SolarWaterHeater whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SolarWaterHeater whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SolarWaterHeater extends Model
{
}
