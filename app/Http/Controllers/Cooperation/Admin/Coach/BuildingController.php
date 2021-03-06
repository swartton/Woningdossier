<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Helpers\Hoomdossier;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Cooperation;
use App\Services\BuildingCoachStatusService;

class BuildingController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $connectedBuildingsForUser = BuildingCoachStatusService::getConnectedBuildingsByUser(Hoomdossier::user())->pluck('building_id');

        // we do the sort on the collection, this would be another "complicated" query.
        // for now this will do.
        $buildings = Building::findMany($connectedBuildingsForUser)
            ->load([
                    'user',
                    'buildingStatuses' => function ($q) {
                        $q->with('status')->mostRecent();
                    }, ]
            )->sortByDesc(function (Building $building) {
                return $building->buildingStatuses->first()->appointment_date;
            });

        return view('cooperation.admin.coach.buildings.index', compact('buildings', 'buildings'));
    }
}
