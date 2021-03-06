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
 * @property string|null $cooperation_email
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ExampleBuilding[] $exampleBuildings
 * @property-read int|null $example_buildings_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Questionnaire[] $questionnaires
 * @property-read int|null $questionnaires_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Step[] $steps
 * @property-read int|null $steps_count
 * @property-read \App\Models\CooperationStyle|null $style
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation whereCooperationEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation whereWebsiteUrl($value)
 * @mixin \Eloquent
 */
class Cooperation extends Model
{
    public $fillable = [
        'name', 'website_url', 'slug', 'cooperation_email',
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
     * Return the questionnaires of a cooperation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questionnaires()
    {
        return $this->hasMany(Questionnaire::class);
    }

    /**
     * Return the example buildings for the cooperation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function exampleBuildings()
    {
        return $this->hasMany(ExampleBuilding::class);
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
     * Get the sub steps for a given step.
     *
     * @return mixed
     */
    public function getSubStepsForStep(Step $step)
    {
        return $this->steps()->subStepsForStep($step)->activeOrderedSteps()->get();
    }

    /**
     * Check if the cooperation has a active step.
     */
    public function isStepActive(Step $step): bool
    {
        $cooperationSteps = $this->steps();
        $cooperationStep = $cooperationSteps->find($step->id);
        if ($cooperationStep instanceof Step) {
            if ($cooperationStep->pivot->is_active) {
                return true;
            }
        }

        return false;
    }

    /**
     * get the active steps with its substeps ordered on the order column.
     *
     * @return Collection|mixed
     */
    public function getActiveOrderedSteps()
    {
        return \App\Helpers\Cache\Cooperation::getActiveOrderedSteps($this);
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
     * Return a collection of users for the cooperation and given role.
     *
     * This does not apply any scopes and should probably only be used in admin environments.
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
