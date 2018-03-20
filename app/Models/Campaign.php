<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Campaign
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Opportunity[] $opportunities
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Campaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Campaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Campaign whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Campaign whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Campaign extends Model
{
    public function opportunities(){
    	return $this->hasMany(Opportunity::class);
    }

}
