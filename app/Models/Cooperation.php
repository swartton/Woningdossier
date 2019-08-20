<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\Models\Cooperation
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $website_url
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Step[] $steps
 * @property-read \App\Models\CooperationStyle $style
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cooperation whereWebsiteUrl($value)
 * @mixin \Eloquent
 */
class Cooperation extends Model
{
    public $fillable
        = [
            'name', 'website_url', 'slug',
        ];

    /**
     * The users associated with this cooperation.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function style()
    {
        return $this->hasOne(CooperationStyle::class);
    }

    /**
     * Get all the steps from the cooperation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function steps()
    {
        return $this->belongsToMany(Step::class, 'cooperation_steps')->withPivot('order', 'is_active');
    }

    /**
     * Check if the cooperation has a active step.
     *
     * @param  Step  $step
     *
     * @return bool
     */
    public function isStepActive(Step $step): bool
    {
        $cooperationSteps = $this->steps();
        $cooperationStep  = $cooperationSteps->find($step->id);
        if ($cooperationStep instanceof Step) {
            if ($cooperationStep->pivot->is_active) {
                return true;
            }
        }

        return false;
    }

    /**
     * get the active steps ordered on the order column.
     *
     * @return mixed
     */
    public function getActiveOrderedSteps(): Collection
    {
        return $this->steps()->orderBy('cooperation_steps.order')->where('cooperation_steps.is_active', '1')->get();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Return the coaches from the current cooperation.
     *
     * @return $this
     */
    public function getCoaches()
    {
        $coaches = $this->users()->role('coach');

        return $coaches;
    }


    /**
     * Return a collection of users for the cooperation and given role
     *
     * This does not apply any scopes and should probably only be used in admin environments.
     *
     * @param  Role  $role
     *
     * @return Collection
     */
    public function getUsersWithRole(Role $role): Collection
    {
        return User::hydrate(
            \DB::table(config('permission.table_names.model_has_roles'))
               ->where('cooperation_id', $this->id)
               ->where('role_id', $role->id)
               ->leftJoin('users', config('permission.table_names.model_has_roles').'.'.config('permission.column_names.model_morph_key'), '=', 'users.id')
               ->get()->toArray()
        );

    }
}
