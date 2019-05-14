<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PrivateMessageView.
 *
 * @property int $id
 * @property int $private_message_id
 * @property int|null $user_id
 * @property int|null $cooperation_id
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView whereCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView wherePrivateMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView whereUserId($value)
 * @mixin \Eloquent
 */
class PrivateMessageView extends Model
{
    protected $fillable = [
        'private_message_id', 'user_id', 'cooperation_id', 'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Get the total unread messages from a auth user.
     *
     * @return int
     */
    public static function getTotalUnreadMessages()
    {
        // if the user is loggen in as a coordinator or cooperation admin
        if (\Auth::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
            return self::where('cooperation_id', HoomdossierSession::getCooperation())
                       ->where('read_at', null)
                       ->count();
        } else {
            return self::where('user_id', \Auth::id())
                       ->where('read_at', null)
                       ->count();
        }
    }

    /**
     * Return the unread messages count for a building id, determined on the auth users his role and id.
     *
     * @param $buildingId
     *
     * @return int
     */
    public static function getTotalUnreadMessagesCountByBuildingIdForAuthUser($buildingId)
    {

        // get ALL the private messages for the given building ids.
        $privateMessagesForBuildingId = PrivateMessage::where('building_id', $buildingId)->get();

        // now get the ALL the private message ids for a building id
        $privateMessageIds = $privateMessagesForBuildingId->pluck('id')->all();

        if (\Auth::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin']) && ! is_null($buildingId)) {
            return self::where('cooperation_id', HoomdossierSession::getCooperation())
                       ->whereIn('private_message_id', $privateMessageIds)
                       ->whereNull('read_at')
                       ->count();
        } else {


            return self::where('user_id', \Auth::id())
                       ->whereIn('private_message_id', $privateMessageIds)
                       ->whereNull('read_at')
                       ->count();
        }
    }

    /**
     * Return the unread messages count for a user on a building
     *
     * @param $buildingId
     *
     * @return int
     */
    public static function getTotalUnreadMessagesCountByBuildingId($buildingId)
    {

        $building = Building::find($buildingId);
        if ($building instanceof Building) {

            // get ALL the private messages for the given building ids.
            $privateMessagesForBuildingId = PrivateMessage::where('building_id', $buildingId)->get();

            // now get the ALL the private message ids for a building id
            $privateMessageIds = $privateMessagesForBuildingId->pluck('id')->all();

            return self::where('user_id', $building->user_id)
                       ->whereIn('private_message_id', $privateMessageIds)
                       ->whereNull('read_at')
                       ->count();
        }
    }

    public static function isMessageUnread($privateMessage)
    {
        // if the user is loggen in as a coordinator or cooperation admin
        if (\Auth::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
            $privateMessageView = self::where('private_message_id', $privateMessage->id)
                                      ->where('cooperation_id', HoomdossierSession::getCooperation())->first();
            if ($privateMessageView instanceof self && is_null($privateMessageView->read_at)) {
                return true;
            }

            return false;
        } else {
            $privateMessageView = self::where('private_message_id', $privateMessage->id)->where('user_id',
                \Auth::id())->first();
            if ($privateMessageView instanceof self && is_null($privateMessageView->read_at)) {
                return true;
            }

            return false;
        }
    }
}
