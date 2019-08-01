<?php

namespace App\Services;

use App\Helpers\Str;
use App\Models\Building;
use App\Models\InputSource;

class BuildingDataCopyService
{

    public static function copy(
        Building $building,
        InputSource $from,
        InputSource $to
    ) {

        // the tables that have a the where_column is used to query on the resident his answers.
        $tables = [
            'building_features',
            'building_elements'           => [
                'where_column'            => 'element_id',
                'additional_where_column' => 'element_value_id',
            ],
            'building_services'           => [
                'where_column'            => 'service_id',
                'additional_where_column' => 'service_value_id',
            ],
            'building_roof_types'         => [
                'where_column' => 'roof_type_id',
            ],
            'building_insulated_glazings' => [
                'where_column' => 'measure_application_id',
            ],
            'building_user_usages',
            'building_paintwork_statuses',
            'building_user_usages',
            'user_progresses'             => [
                'where_column' => 'step_id',
            ],
            'questions_answers'           => [
                'where_column' => 'question_id',
            ],
            'building_features',
            'building_pv_panels',
            'building_heaters',
            'building_appliances',

            'user_action_plan_advices' => [
                'where_column' => 'measure_application_id',
            ],
            'user_energy_habits',
            'user_interests'           => [
                'where_column'            => 'interested_in_type',
                'additional_where_column' => 'interested_in_id',
            ],
        ];

        foreach ($tables as $tableOrInt => $tableOrWhereColumns) {
            // now check if its a int
            // if it isn't a int, the $tableOrId is a table and the $tableOrWhereColumns is a where column
            // else the $tableOrWhereColumns is the table and we do not need to query further.
            if ( ! is_int($tableOrInt)) {
                $table       = $tableOrInt;
                $whereColumn = $tableOrWhereColumns['where_column'];
            } else {
                $table = $tableOrWhereColumns;
            }
            // building to copy data from
            $user     = $building->user()->first();

            // set the building or user id, depending on which column exists on the table
            if (\Schema::hasColumn($table, 'user_id')) {
                $buildingOrUserId     = $user->id;
                $buildingOrUserColumn = 'user_id';
            } else {
                $buildingOrUserId     = $building->id;
                $buildingOrUserColumn = 'building_id';
            }


            // now we get all the answers from the desired input source
            $fromValues = \DB::table($table)
                             ->where('input_source_id', $from->id)
                             ->where($buildingOrUserColumn, $buildingOrUserId)
                             ->get();

            // now check if the $whereColumn isset
            // if so we need to add it to the query from the resident during the loop from the $fromValues
            if (isset($whereColumn)) {
                // loop through the answers from the desired input source
                foreach ($fromValues as $fromValue) {
                    if ($fromValue instanceof \stdClass && isset($fromValue->$whereColumn)) {
                        // now build the query to get the resident his answers
                        $toValueQuery = \DB::table($table)
                                           ->where('input_source_id', $to->id)
                                           ->where($buildingOrUserColumn,
                                               $buildingOrUserId)
                                           ->where($whereColumn,
                                               $fromValue->$whereColumn);

                        // count the rows
                        $toValueCount = \DB::table($table)
                                           ->where('input_source_id', $to->id)
                                           ->where($buildingOrUserColumn,
                                               $buildingOrUserId)
                                           ->where($whereColumn,
                                               $fromValue->$whereColumn)
                                           ->count();


                        // if there are multiple, then we need to add another where to the query.
                        // else, we dont need to query further an can get the first result and use that to update it.
                        if ($toValueCount > 1) {
                            $additionalWhereColumn = $tableOrWhereColumns['additional_where_column'];
                            // add the where to the query
                            $toValueQuery = $toValueQuery
                                ->where($additionalWhereColumn,
                                    $fromValue->$additionalWhereColumn);


                            // get the result
                            $toValue = $toValueQuery->first();

                            // cast the results to a array
                            $toValue   = (array) $toValue;
                            $fromValue = (array) $fromValue;

                            // if it exists, we need to update it. Else we need to insert a new row.
                            if ( ! empty($toValue)) {
                                $toValueQuery->update(static::createUpdateArray($toValue,
                                    $fromValue));
                            } else {
                                // we cant create an update array since there is no data from the target input source
                                // unset the stuff we dont want to insert
                                unset($fromValue['id'], $fromValue['input_source_id']);
                                // change the input source id to the resident
                                $fromValue['input_source_id'] = $to->id;
                                // and insert a new row!
                                \DB::table($table)->insert($fromValue);
                            }
                        } else {
                            $toValue = $toValueQuery->first();

                            // cast the results to a array
                            $toValue   = (array) $toValue;
                            $fromValue = (array) $fromValue;

                            // YAY! data has been copied so update or create the target input source his records.
                            if ($toValueQuery->first() instanceof \stdClass) {
                                // check if its empty ornot.
                                if ( ! empty($updateData = static::createUpdateArray((array) $toValue,
                                    (array) $fromValue))) {
                                    $toValueQuery->update($updateData);
                                }
                            } else {
                                // we cant create an update array since there is no data from the target input source
                                // unset the stuff we dont want to insert
                                unset($fromValue['id'], $fromValue['input_source_id']);
                                // change the input source id to the resident
                                $fromValue['input_source_id'] = $to->id;
                                // and insert a new row!
                                \DB::table($table)->insert($fromValue);
                            }
                        }
                    }
                }
            } else {
                // get the resident his input
                $toValueQuery = \DB::table($table)
                                   ->where('input_source_id', $to->id)
                                   ->where($buildingOrUserColumn,
                                       $buildingOrUserId);


                // get the first result from the desired input source
                $fromValue = $fromValues->first();
                $toValue   = $toValueQuery->first();


                if ($toValue instanceof \stdClass) {
                    if ( ! empty($updateData = static::createUpdateArray((array) $toValue,
                        (array) $fromValue))) {
                        $toValueQuery->update($updateData);
                    }
                } else {
                    $fromValue = (array) $fromValue;
                    // unset the stuff we dont want to insert
                    unset($fromValue['id'], $fromValue['input_source_id']);
                    // change the input source id to the resident
                    $fromValue['input_source_id'] = $to->id;

                    // and insert a new row!
                    \DB::table($table)->insert($fromValue);
                }
            }
        }

    }

    /**
     * Check if a key / column name needs a update.
     *
     * @param $key  | Column name
     *
     * @return bool
     */
    private static function keyNeedsUpdate($key)
    {
        $keysToNotUpdate = [
            'id', 'building_id', 'input_source_id', 'created_at', 'updated_at',
            'comment', 'additional_info'
        ];
        if ( ! in_array($key, $keysToNotUpdate, true)) {
            return true;
        }

        return false;
    }

    /**
     * Returns whether or not fields are expressed as radio inputs. These fields
     * might have a value of 0, which is considered empty, but in the context of
     * radio buttons they should not be considered empty. We define these
     * fields here so we can test for !empty OR radio input.
     *
     * @param  string  $key
     *
     * @return bool
     */
    private static function isRadioInput($key)
    {
        return in_array($key,
            ['cavity_wall', 'monument', 'facade_plastered_painted']);
    }

    /**
     * Creates an update array from the input source to copy and the input source to update.
     *
     * @param $inputSourceToUpdate
     * @param $inputSourceToCopy
     *
     * @return array
     */
    private static function createUpdateArray(
        $inputSourceToUpdate,
        $inputSourceToCopy
    ): array {
        $updateArray = [];

        // if the desired input source has a extra key and its not empty, then we start to compare and merge the extra column.
        if (array_key_exists('extra',
                $inputSourceToCopy) && ! empty($inputSourceToCopy['extra'])) {
            // if the resident had nothing then we just use the desired input source extra.
            if (empty($inputSourceToUpdate['extra'])) {
                $updateExtra = $inputSourceToCopy['extra'];
            } else {
                $inputSourceToCopyExtra   = json_decode($inputSourceToCopy['extra'],
                    true);
                $inputSourceToUpdateExtra = json_decode($inputSourceToUpdate['extra'],
                    true);

                // filter the values which are not considered to be empty.
                $inputSourceToCopyNotNullExtraValues = array_filter($inputSourceToCopyExtra,
                    function ($extraValue, $extraKey) {
                        // if the string is not considered empty, then we want to update it.
                        return ! Str::isConsideredEmptyAnswer($extraValue) && static::keyNeedsUpdate($extraKey);
                    }, ARRAY_FILTER_USE_BOTH);

                // create the extra column json.
                // merge those toes
                $updateExtra = json_encode(array_merge($inputSourceToUpdateExtra,
                    $inputSourceToCopyNotNullExtraValues));
            }

            // add the json to the extra ket
            $updateArray['extra'] = $updateExtra;
            // unset the id and extra, we dont need it anymore.
            unset($inputSourceToUpdate['id'], $inputSourceToCopy['extra']);
        }

        // now update the "normal" values
        foreach ($inputSourceToCopy as $desiredInputSourceKey => $desiredInputSourceAnswer) {
            // if the answer from the desired input source is not empty and the key needs a update, then we update the resident his answer.
            if (( ! empty($desiredInputSourceAnswer) || static::isRadioInput($desiredInputSourceKey)) && static::keyNeedsUpdate($desiredInputSourceKey)) {
                $updateArray[$desiredInputSourceKey] = $desiredInputSourceAnswer;
            }
        }

        return $updateArray;
    }
}