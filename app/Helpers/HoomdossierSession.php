<?php

namespace App\Helpers;

use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Role;
use Illuminate\Support\Facades\Session;

class HoomdossierSession extends Session {


    /**
     * Set all the required values.
     *
     * @param Building $building
     * @param InputSource $inputSource
     * @param InputSource $inputSourceValue
     * @param Role $role
     */
    public static function setHoomdossierSessions(Building $building, InputSource $inputSource, InputSource $inputSourceValue, Role $role)
    {
        self::setBuilding($building);
        self::setInputSource($inputSource);
        self::setInputSourceValue($inputSourceValue);
        self::setRole($role);
    }

    /**
     * Destroy the hoomdossier sessions
     */
    public static function destroy()
    {
//        self::forget(['hoomdossier_session.role_id', 'hoomdossier_session.building_id', 'hoomdossier_session.input_source_id', 'hoomdossier_session.input_source_value_id']);
        self::forget(['hoomdossier_session']);
    }

    /**
     * Set the Cooperation id
     *
     * @param Cooperation $cooperation
     */
    public static function setCooperation(Cooperation $cooperation)
    {
        self::put('cooperation', $cooperation->id);
    }

    /**
     * Check if the cooperation session is set
     *
     * @return bool
     */
    public static function hasCooperation(): bool
    {
        if (self::has('cooperation')) {
            return true;
        }

        return false;
    }

    public static function getCooperation(): int
    {
        return self::get('cooperation');
    }

    /**
     * Function to set Hoomdossier sessions.
     *
     * @param $key
     * @param $value
     */
    public static function setHoomdossierSession($key, $value)
    {
        self::put('hoomdossier_session.'.$key, $value);
    }

    /**
     * Get a value from the Hoomdossier session
     *
     * @param $key
     * @return mixed
     */
    public static function getHoomdossierSession($key)
    {
        return self::get('hoomdossier_session.'.$key);
    }

	/**
	 * Returns whether or not this session contains a current role.
	 *
	 * @return bool
	 */
	public static function hasRole() : bool
	{
		return !empty(self::getRole());
	}

    /**
     * Set the role
     *
     * @param Role $role
     */
    public static function setRole(Role $role)
    {
        self::setHoomdossierSession('role_id', $role->id);
    }

    /**
     * Set the input source value id
     * @NOTE: this is not the same as the input source, this input source will be used to get the right values for the form.
     *
     * @param InputSource $inputSource
     */
    public static function setInputSourceValue(InputSource $inputSource)
    {
        self::setHoomdossierSession('input_source_value_id', $inputSource->id);
    }

    /**
     * Set the input source id
     *
     * @param InputSource $inputSource
     */
    public static function setInputSource(InputSource $inputSource)
    {
    	self::setHoomdossierSession( 'input_source_id', $inputSource->id );
    }

    /**
     * Set the building id
     *
     * @param Building $building
     */
    public static function setBuilding(Building $building)
    {
        self::setHoomdossierSession('building_id', $building->id);
    }

	/**
	 * Returns the set role_id.
	 *
	 * @return int|null
	 */
    public static function getRole()
    {
        return self::getHoomdossierSession('role_id');
    }

    public static function currentRole($column = 'name') : string
    {
    	$roleId = self::getRole();
		if (!empty($roleId)){
			$role = Role::find($roleId);
			if ($role instanceof Role){
				$result = $role->getAttribute($column);
				if (!empty($result)){
					return $result;
				}
			}
		}
		return "";
    }

	/**
	 * Returns whether or not this session contains a current role.
	 *
	 * @return bool
	 */
    public static function hasRole() : bool
    {
    	return !empty(self::getRole());
    }

    /**
     * Get the input source id
     *
     * @return int
     */
    public static function getInputSource(): int
    {
        return self::getHoomdossierSession('input_source_id');
    }

    /**
     * Get the input source value id
     * Read the NOTE @setInputSourceValue
     * @return int
     */
    public static function getInputSourceValue(): int
    {
        return self::getHoomdossierSession('input_source_value_id');
    }

    /**
     * Get the building id
     *
     * @return int|null
     */
    public static function getBuilding()
    {
        return self::getHoomdossierSession('building_id');
    }

    /**
     * Return the Hoomdossier session data
     *
     * @return array
     */
    public static function getAll(): array
    {
        return self::get('hoomdossier_session');
    }
}