<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserProgress
 *
 * @property int $id
 * @property int $user_id
 * @property int $step_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProgress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProgress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProgress whereStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProgress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProgress whereUserId($value)
 * @mixin \Eloquent
 */
class UserProgress extends Model
{
    //
}