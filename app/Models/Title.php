<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Title
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\People[] $people
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Title whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Title whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Title whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Title whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Title extends Model
{

	public $fillable = ['name', ];

	public function users(){
		return $this->hasMany(User::class);
	}

	public function people(){
		return $this->hasMany(People::class);
	}
}
