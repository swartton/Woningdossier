<?php

namespace App\Jobs;

use App\Exports\Cooperation\CsvExport;
use App\Helpers\HoomdossierSession;
use App\Helpers\Str;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Services\CsvService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;

class GenerateTotalReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cooperation;
    protected $anonymizeData;
    protected $fileType;
    protected $fileStorage;

    /**
     *
     * @param  Cooperation  $cooperation
     * @param  FileStorage $fileStorage
     * @param  FileType $fileType
     * @param  bool  $anonymizeData
     */
    public function __construct(Cooperation $cooperation, FileType $fileType, FileStorage $fileStorage,  bool $anonymizeData = false)
    {
        $this->fileType = $fileType;
        $this->fileStorage = $fileStorage;
        $this->cooperation = $cooperation;
        $this->anonymizeData = $anonymizeData;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (\App::runningInConsole()) {
            \Log::debug(__CLASS__.' Is running in the console with a maximum execution time of: '.ini_get('max_execution_time'));
        }

        // temporary session to get the right data for the dumb.
        $residentInputSource = InputSource::findByShort('resident');
        HoomdossierSession::setInputSource($residentInputSource);
        HoomdossierSession::setInputSourceValue($residentInputSource);

        $rows = CsvService::totalReport($this->cooperation, $this->anonymizeData);

        \Session::forget('hoomdossier_session');

        // export the csv file
        Excel::store(new CsvExport($rows), $this->fileStorage->filename, 'downloads', \Maatwebsite\Excel\Excel::CSV);


        $this->fileStorage->isProcessed();
    }

    public function failed(\Exception $exception)
    {
        $this->fileStorage->delete();
    }

}
