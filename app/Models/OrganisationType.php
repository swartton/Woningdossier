<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrganisationType
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Organisation[] $organisations
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrganisationType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrganisationType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrganisationType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrganisationType whereUpdatedAt($value)
 */
class OrganisationType extends Model
{

	public $fillable = ['name', ];

	public function organisations(){
		return $this->hasMany(Organisation::class);
	}
}
