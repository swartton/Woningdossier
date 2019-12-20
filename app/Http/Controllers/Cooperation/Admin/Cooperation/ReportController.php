<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileTypeCategory;
use App\Models\InputSource;
use App\Services\CsvService;

class ReportController extends Controller
{
    public function index()
    {
        $reportFileTypeCategory = FileTypeCategory::short('report')
            ->with(['fileTypes' => function ($query) {
                $query->where('short', '!=', 'pdf-report')
                    ->with(['files' => function ($query) {
                        $query->leaveOutPersonalFiles();
                    }]);
            }])->first();

        // Is there any file being processed for my cooperation
        $anyFilesBeingProcessed = FileStorage::leaveOutPersonalFiles()->withExpired()->beingProcessed()->count();

        dd(CsvService::totalReport(Cooperation::find(1), InputSource::findByShort(InputSource::RESIDENT_SHORT), false));

        return view('cooperation.admin.cooperation.reports.index', compact('reportFileTypeCategory', 'anyFilesBeingProcessed'));
    }
}
