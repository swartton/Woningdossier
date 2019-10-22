<?php

namespace App\Helpers;

use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Role;
use Illuminate\Support\Facades\Session;

class HoomdossierSession extends Session
{
    /**
     * Set all the required values.
     *
     * @param Building    $building
     * @param InputSource $inputSource
     * @param InputSource $inputSourceValue
     * @param Role        $role
     */
    public static function setHoomdossierSessions(Building $building, InputSource $inputSource, InputSource $inputSourceValue, Role $role)
    {
        self::setBuilding($building);
        self::setInputSource($inputSource);
        self::setInputSourceValue($inputSourceValue);
        self::setRole($role);
        self::setCompareInputSourceShort($inputSource->short);
        // default to false
        self::setIsUserComparingInputSources(false);

        self::setIsObserving(false);
    }

    /**
     * Destroy the hoomdossier sessions.
     */
    public static function destroy()
    {
        self::forget(['hoomdossier_session']);
    }

    /**
     * Set the Cooperation id.
     *
     * @param Cooperation $cooperation
     */
    public static function setCooperation(Cooperation $cooperation)
    {
        self::put('cooperation', $cooperation->id);
    }

    /**
     * Check if the cooperation session is set.
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

    /**
     * @param bool $object Set to true if you want to get an object back
     *
     * @return int|Cooperation|null
     */
    public static function getCooperation($object = false)
    {
        $cooperation = self::get('cooperation');

        // if there is no cooperation set and the application is not running in the console, we have a serious issue.
        if (! is_int($cooperation) && ! app()->runningInConsole()) {
            \Log::error('Cooperation was not an integer!! ');
            \Log::error($cooperation);
        }

        if ($object) {
            $cooperation = \App\Helpers\Cache\Cooperation::find($cooperation);
        }

        return $cooperation;
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
     * Get a value from the Hoomdossier session.
     *
     * @param $key
     * @param $default
     *
     * @return mixed
     */
    public static function getHoomdossierSession($key, $default = null)
    {
        return self::get('hoomdossier_session.'.$key, $default);
    }

    /**
     * Returns whether or not this session contains a current role.
     *
     * @return bool
     */
    public static function hasRole(): bool
    {
        return ! empty(self::getRole());
    }

    /**
     * Set the role.
     *
     * @param Role $role
     */
    public static function setRole(Role $role)
    {
        self::setHoomdossierSession('role_id', $role->id);
    }

    /**
     * Set the observing boolean.
     *
     * @NOTE key meant to determine if a user is observing someones tool / building.
     *
     * @param bool $observing
     */
    public static function setIsObserving(bool $observing = false)
    {
        self::setHoomdossierSession('is_observing', $observing);
    }

    /**
     * Check if a user is observing someones tool / building.
     *
     * @return bool
     */
    public static function getIsObserving(): bool
    {
        return self::getHoomdossierSession('is_observing', false);
    }

    /**
     * Set the input source value id.
     *
     * @NOTE: this is not the same as the input source, this input source will be used to get the right values for the form.
     *
     * @param InputSource $inputSource
     */
    public static function setInputSourceValue(InputSource $inputSource)
    {
        self::setHoomdossierSession('input_source_value_id', $inputSource->id);
    }

    /**
     * Set the input source id.
     *
     * @param InputSource $inputSource
     */
    public static function setInputSource(InputSource $inputSource)
    {
        self::setHoomdossierSession('input_source_id', $inputSource->id);
    }

    /**
     * Set the bool, this determines if the logged in user is comparing input sources.
     *
     * @param bool $isUserComparing
     */
    public static function setIsUserComparingInputSources(bool $isUserComparing)
    {
        self::setHoomdossierSession('is_user_comparing_input_sources', $isUserComparing);
    }

    /**
     * Set the compare input source short, this is used to retrieve the right compare value from the dom.
     *
     * @param string $inputSourceShort
     */
    public static function setCompareInputSourceShort(string $inputSourceShort)
    {
        self::setHoomdossierSession('compare_input_source_short', $inputSourceShort);
    }

    /**
     * Stop / Reset the sessions for comparing the input sources
     * We set the compareInputSourceShort back to the auth user his own input source short and the isUserComparingInputSource back to false.
     */
    public static function stopUserComparingInputSources()
    {
        self::setIsUserComparingInputSources(false);
        self::setCompareInputSourceShort(InputSource::find(self::getInputSource())->short);
    }

    /**
     * Get the compare input source.
     *
     * @return string
     */
    public static function getCompareInputSourceShort(): string
    {
        return self::getHoomdossierSession('compare_input_source_short', '');
    }

    /**
     * Set the building id.
     *
     * @param Building $building
     */
    public static function setBuilding(Building $building)
    {
        self::setHoomdossierSession('building_id', $building->id);
    }

    /**
     * Returns the role or role_id.
     *
     * @param bool $object Set to true if you want an object returned
     *
     * @return int|Role|null
     */
    public static function getRole($object = false)
    {
        $id = self::getHoomdossierSession('role_id');
        if (! $object) {
            return $id;
        }

        return \App\Helpers\Cache\Role::find($id);
    }

    public static function currentRole($column = 'name'): string
    {
        $roleId = self::getRole();
        if (! empty($roleId)) {
            $role = Role::find($roleId);
            if ($role instanceof Role) {
                $result = $role->getAttribute($column);
                if (! empty($result)) {
                    return $result;
                }
            }
        }

        return '';
    }

    /**
     * Get the input source id.
     *
     * @param bool $object Set to true if you want an object returned
     *
     * @return int|InputSource|null
     */
    public static function getInputSource($object = false)
    {
        $id = self::getHoomdossierSession('input_source_id');
        if (! $object) {
            return $id;
        }

        return \App\Helpers\Cache\InputSource::find($id);
    }

    /**
     * Get the input source value id
     * Read the NOTE @setInputSourceValue.
     *
     * @return int|InputSource|null
     */
    public static function getInputSourceValue($object = false)
    {
        $id = self::getHoomdossierSession('input_source_value_id');

        if (! $object) {
            return $id;
        }

        return \App\Helpers\Cache\InputSource::find($id);
    }

    /**
     * Get the building id.
     *
     * @param bool $object Set to true if you want to get an object back
     *
     * @return int|Building|null
     */
    public static function getBuilding($object = false)
    {
        $building = self::getHoomdossierSession('building_id');
        if ($object) {
            $building = Building::find($building);
        }

        return $building;
    }

    /**
     * Returns if a user is comparing input sources.
     *
     * @return bool
     */
    public static function getIsUserComparingInputSources(): bool
    {
        return self::getHoomdossierSession('is_user_comparing_input_sources', false);
    }

    /**
     * Check if a user is comparing his own values against the values from a other input source.
     *
     * @return bool
     */
    public static function isUserComparingInputSources(): bool
    {
        return self::getIsUserComparingInputSources();
    }

    /**
     * Check if a user is NOT comparing his own values against the values from a other input source.
     *
     * @return bool
     */
    public static function isUserNotComparingInputSources(): bool
    {
        return ! self::getIsUserComparingInputSources();
    }

    /**
     * Check if a user is observing a building / tool.
     *
     * @return bool
     */
    public static function isUserObserving(): bool
    {
        return self::getIsObserving();
    }

    /**
     * Return the Hoomdossier session data.
     *
     * @return array
     */
    public static function getAll(): array
    {
        return (array) self::get('hoomdossier_session');
    }
}
