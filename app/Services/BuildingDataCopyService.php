<?php

namespace App\Services;

use App\Helpers\Str;
use App\Models\Building;
use App\Models\InputSource;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class BuildingDataCopyService
{
    /**
     * Method to copy data from a building and input source to a other input source on the same building.
     */
    public static function copy(Building $building, InputSource $from, InputSource $to)
    {
        // the tables that have a the where_column is used to query on the resident his answers.
        $tables = [
            'user_interests' => [
                'where_column' => 'interested_in_id',
                'additional_where_column' => 'interested_in_type',
            ],

            'building_elements' => [
                'where_column' => 'element_id',
                'additional_where_column' => 'element_value_id',
            ],
            'building_services' => [
                'where_column' => 'service_id',
                'additional_where_column' => 'service_value_id',
            ],
            'building_roof_types' => [
                'where_column' => 'roof_type_id',
            ],
            'building_insulated_glazings' => [
                'where_column' => 'measure_application_id',
            ],
            'building_paintwork_statuses',
            'completed_steps' => [
                'where_column' => 'step_id',
            ],
            'questions_answers' => [
                'where_column' => 'question_id',
            ],
            'building_ventilations',
            'building_features',
            'building_pv_panels',
            'building_heaters',
            'building_appliances',

            'user_action_plan_advices' => [
                'where_column' => 'measure_application_id',
                // could be added but doesnt need to be
                'additional_where_column' => 'step_id',
            ],

            'user_energy_habits',
        ];

        foreach ($tables as $tableOrInt => $tableOrWhereColumns) {
            // if the $tableOrInt is an int the $tableOrWhereColumns contains a table, else it contains where columns which we will need later on.
            if (is_int($tableOrInt)) {
                $table = $tableOrWhereColumns;
            } else {
                $table = $tableOrInt;
            }
            Log::debug("Copy: table {$table}");
            // building to copy data from
            $user = $building->user()->first();

            // set the building or user id, depending on which column exists on the table
            if (\Schema::hasColumn($table, 'user_id')) {
                $buildingOrUserId = $user->id;
                $buildingOrUserColumn = 'user_id';
            } else {
                $buildingOrUserId = $building->id;
                $buildingOrUserColumn = 'building_id';
            }

            // now we get all the answers from the desired input source
            $fromValues = \DB::table($table)
                ->where('input_source_id', $from->id)
                ->where($buildingOrUserColumn, $buildingOrUserId)
                ->get();

            // check if the $tableOrWhereColumns is a array and if a where column exists.
            // if so we need to add it to the query from the resident during the loop from the $fromValues
            if (is_array($tableOrWhereColumns) && array_key_exists('where_column', $tableOrWhereColumns)) {
                $whereColumn = $tableOrWhereColumns['where_column'];

                // loop through the answers from the desired input source
                foreach ($fromValues as $fromValue) {
                    if ($fromValue instanceof \stdClass && isset($fromValue->$whereColumn)) {
                        // now build the query to get the resident his answers
                        $toValueQuery = \DB::table($table)
                            ->where('input_source_id', $to->id)
                            ->where($buildingOrUserColumn, $buildingOrUserId)
                            ->where($whereColumn, $fromValue->$whereColumn);


                        $hasMultipleValues = false;
                        $additionalWhereColumn = null;

                        // if there are multiple values to copy from we (probably) need to query further
                        // we also check if the additional_where_column is actually set, this way we can narrow the result down to 1 row.

                        // check if there is an additional column to query on
                        if (isset($tableOrWhereColumns['additional_where_column'])) {

                            // if so, we need to check if there are multiple values
                            // fi there is a case where the from OR to has multiple values we need to add the additional column to the update thing
                            $additionalWhereColumn = $tableOrWhereColumns['additional_where_column'];
                            $hasMultipleValues = $toValueQuery->count() > 1;

                            if ($hasMultipleValues === false) {
                                $hasMultipleValues = \DB::table($table)
                                    ->where('input_source_id', $from->id)
                                    ->where($buildingOrUserColumn, $buildingOrUserId)
                                    ->where($whereColumn, $fromValue->$whereColumn)
                                    ->count() > 1;
                            }

                        }
                        if ($hasMultipleValues) {
                            // add the where to the query
                            $toValueQuery = $toValueQuery->where($additionalWhereColumn, $fromValue->$additionalWhereColumn);

                            // get the result
                            $toValue = $toValueQuery->first();

                            // cast the results to a array
                            $toValue = (array) $toValue;
                            $fromValue = (array) $fromValue;

                            // if it exists, we need to update it. Else we need to insert a new row.
                            if (! empty($toValue)) {
                                $toValueQuery->update(static::createUpdateArray($toValue, $fromValue));
                            } else {
                                $fromValue = static::createUpdateArray((array) $toValue, (array) $fromValue);
                                // change the input source id to the 'to' id
                                $fromValue['input_source_id'] = $to->id;
                                $fromValue[$buildingOrUserColumn] = $buildingOrUserId;
                                // and insert a new row!
                                \DB::table($table)->insert($fromValue);
                            }
                        } else {
                            $toValue = $toValueQuery->first();

                            // cast the results to a array
                            $toValue = (array) $toValue;
                            $fromValue = (array) $fromValue;

                            // YAY! data has been copied so update or create the target input source his records.
                            if ($toValueQuery->first() instanceof \stdClass) {
                                // check if its empty ornot.
                                if (! empty($updateData = static::createUpdateArray((array) $toValue, (array) $fromValue))) {
                                    $toValueQuery->update($updateData);
                                }
                            } else {
                                $fromValue = static::createUpdateArray((array) $toValue, (array) $fromValue);
                                // change the input source id to the 'to' id
                                $fromValue['input_source_id'] = $to->id;
                                $fromValue[$buildingOrUserColumn] = $buildingOrUserId;
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
                    ->where($buildingOrUserColumn, $buildingOrUserId);

                // get the first result from the desired input source
                $fromValue = $fromValues->first();
                $toValue = $toValueQuery->first();

                if ($toValue instanceof \stdClass) {
                    if (! empty($updateData = static::createUpdateArray((array) $toValue, (array) $fromValue))) {
                        $toValueQuery->update($updateData);
                    }
                } else {
                    $fromValue = (array) $fromValue;
                    // unset the stuff we dont want to insert
                    $fromValue = static::createUpdateArray((array) $toValue, (array) $fromValue);
                    // change the input source id to the 'to' id
                    $fromValue['input_source_id'] = $to->id;
                    $fromValue[$buildingOrUserColumn] = $buildingOrUserId;

                    // and insert a new row!
                    \DB::table($table)->insert($fromValue);
                }
            }
        }

        Artisan::call('fix:duplicates');
    }

    /**
     * Check if a key / column name needs a update.
     *
     * @param string $key Column name
     *
     * @return bool
     */
    private static function keyNeedsUpdate($key)
    {
        $keysToNotUpdate = [
            'id', 'building_id', 'input_source_id', 'created_at', 'updated_at', 'comment', 'additional_info',
            'living_situation_extra',
        ];

        // if the key does exists in the array it does not need a update
        if (in_array($key, $keysToNotUpdate, true)) {
            return false;
        }

        return true;
    }

    /**
     * Returns whether or not fields are expressed as radio inputs. These fields
     * might have a value of 0, which is considered empty, but in the context of
     * radio buttons they should not be considered empty. We define these
     * fields here so we can test for !empty OR radio input.
     *
     * @param string $key
     *
     * @return bool
     */
    private static function isRadioInput($key)
    {
        return in_array($key, ['cavity_wall', 'monument', 'facade_plastered_painted']);
    }

    /**
     * Method to filter the value's from the extra column.
     *
     * @param $extraColumnData
     */
    private static function filterExtraColumn($extraColumnData): array
    {
        return array_filter($extraColumnData, function ($extraValue, $extraKey) {
            // if the string is not considered empty, and its need an update. Then we add it
            return ! Str::isConsideredEmptyAnswer($extraValue) && static::keyNeedsUpdate($extraKey);
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Creates an update array from the input source to copy and the input source to update.
     *
     * @param $inputSourceToUpdate
     * @param $inputSourceToCopy
     */
    private static function createUpdateArray($inputSourceToUpdate, $inputSourceToCopy): array
    {
        $updateArray = [];

        // if the desired input source has a extra key and its not empty, then we start to compare and merge the extra column.
        if (array_key_exists('extra', $inputSourceToCopy) && ! empty($inputSourceToCopy['extra']) && is_array($inputSourceToCopy['extra'])) {
            if (empty($inputSourceToUpdate['extra'])) {
                $inputSourceToCopyExtra = json_decode($inputSourceToCopy['extra'], true);

                // filter the values which are not considered to be empty.
                $inputSourceToCopyNotNullExtraValues = static::filterExtraColumn($inputSourceToCopyExtra);

                $updateExtra = json_encode($inputSourceToCopyNotNullExtraValues);
            } else {
                $inputSourceToCopyExtra = json_decode($inputSourceToCopy['extra'], true);
                $inputSourceToUpdateExtra = json_decode($inputSourceToUpdate['extra'], true);

                $inputSourceToCopyNotNullExtraValues = static::filterExtraColumn($inputSourceToCopyExtra);

                // set some default stuff
                if (is_null($inputSourceToUpdateExtra)) {
                    $inputSourceToUpdateExtra = [];
                }

                // create the extra column json.
                // merge those toes
                $updateExtra = json_encode(array_merge($inputSourceToUpdateExtra, $inputSourceToCopyNotNullExtraValues));
            }

            // add the json to the extra ket
            $updateArray['extra'] = $updateExtra;
            // unset the id and extra, we dont need it anymore.
            unset($inputSourceToUpdate['id'], $inputSourceToCopy['extra']);
        }

        // now update the "normal" values
        foreach ($inputSourceToCopy as $desiredInputSourceKey => $desiredInputSourceAnswer) {
            // if the answer from the desired input source is not empty and the key needs a update, then we update the resident his answer.
            if ((! empty($desiredInputSourceAnswer) || static::isRadioInput($desiredInputSourceKey)) && static::keyNeedsUpdate($desiredInputSourceKey)) {
                $updateArray[$desiredInputSourceKey] = $desiredInputSourceAnswer;
            }
        }

        return $updateArray;
    }
}
