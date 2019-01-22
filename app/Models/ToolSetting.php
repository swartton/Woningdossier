<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ToolSetting.
 *
 * @property int $id
 * @property int $changed_input_source_id
 * @property int $building_id
 * @property bool $has_changed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Models\InputSource $inputSource
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting whereChangedInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting whereHasChanged($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ToolSetting extends Model
{
    protected $fillable = [
        'changed_input_source_id', 'has_changed', 'building_id',
    ];

    protected $casts = [
        'has_changed' => 'bool',
    ];

    /**
     * check if its changed.
     *
     * @return bool
     */
    public function hasChanged(): bool
    {
        return $this->has_changed;
    }

    /**
     * Return a collection of tool settings for a building where is is not the current inputsource.
     *
     * @param int $buildingId
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getChangedSettings(int $buildingId)
    {
        $toolSettings = self::where('building_id', $buildingId)
            ->where('changed_input_source_id', '!=', HoomdossierSession::getInputSource())
            ->get();

        return $toolSettings;
    }

    /**
     * Get the input source from the tool setting.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inputSource()
    {
        return $this->belongsTo(InputSource::class, 'changed_input_source_id');
    }

    /**
     * Return the tool settings where has_changed is true.
     *
     * @param int $buildingId
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getUndoneChangedSettings(int $buildingId)
    {
        return self::getChangedSettings($buildingId)->where('has_changed', true);
    }
}
