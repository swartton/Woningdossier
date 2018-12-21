<?php

namespace App\Scopes;

use App\Helpers\HoomdossierSession;
use App\Models\InputSource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class GetValueScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model   $model
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $inputSourceValueId = HoomdossierSession::getInputSourceValue();

        $builder->where('input_source_id', $inputSourceValueId);

        // On login, the user input source id and input source value id will be set to the same input source id.
        // The input source value id will be changed when a user changes the input source id by himself
        // so if the input source id != input source value id the user changed it and we can just do a where

        // First do a check if the input source is a resident, because else we dont need to get the best input source.
//        if (InputSource::find(HoomdossierSession::getInputSource())->order == 1) {
//            $builder->where('input_source_id', $inputSourceValueId);
//            if ($inputSourceId != $inputSourceValueId) {
//                $builder->where('input_source_id', $inputSourceValueId);
//            } else {
                // Else we will get the best input source.
                // get the input sources from the current
//                $builder->leftJoin('input_sources', 'input_sources.id', '=', $model->getTable() . '.input_source_id')
//                    ->orderBy('input_sources.order')
//                    ->select('input_sources.id as input_source_id', 'input_sources.name as input_source_name', 'input_sources.short as input_source_short', 'input_sources.order as input_source_order', $model->getTable() . '.*');
//                $builder->where('in')
//            }
//        } else {
//        }
    }
}