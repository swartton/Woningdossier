<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\ToolSettingTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingHeater.
 *
 * @property int                                 $id
 * @property int                                 $building_id
 * @property int|null                            $input_source_id
 * @property int|null                            $pv_panel_orientation_id
 * @property int|null                            $angle
 * @property \Illuminate\Support\Carbon|null     $created_at
 * @property \Illuminate\Support\Carbon|null     $updated_at
 * @property string|null                         $comment
 * @property \App\Models\Building                $building
 * @property \App\Models\InputSource|null        $inputSource
 * @property \App\Models\PvPanelOrientation|null $orientation
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater forMe(\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater whereAngle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater wherePvPanelOrientationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeater whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingHeater extends Model
{
    use GetValueTrait;
    use GetMyValuesTrait;
    use ToolSettingTrait;

    protected $fillable = [
        'building_id', 'input_source_id', 'pv_panel_orientation_id', 'angle',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function orientation()
    {
        return $this->belongsTo(PvPanelOrientation::class, 'pv_panel_orientation_id', 'id');
    }
}
