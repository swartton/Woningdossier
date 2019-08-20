<?php

namespace App\Helpers\Cache;

class Role
{
    const CACHE_KEY_FIND = 'Role_find_%s';

    /**
     * @param  int  $id
     *
     * @return \App\Models\Role|null
     */
    public static function find($id)
    {
        return \Cache::remember(
            sprintf(static::CACHE_KEY_FIND, $id),
            config('woningdossier.cache.times.default'),
            function () use ($id) {
                return \App\Models\Role::find($id);
            }
        );
    }
}