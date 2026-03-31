<?php

namespace App\Jobs;

use App\Exports\catalog_location_export;
use App\Http\Utils\removeFiles;
use App\Models\report_file;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Excel as MaatwebsiteExcel;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class catalog_location_job implements ShouldQueue
{
    use Queueable;

    protected $idfile;
    protected $filter;
    protected $startTime;
    public function __construct($idfile, $filter)
    {
        //
        $this->idfile = $idfile;
        $this->filter = $filter;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $this->startTime = microtime(true);
        $report_file = report_file::find($this->idfile);

        $path = "Reports/" . $report_file->file_name;
        $file = $this->filter['type'] === 'pdf' ? Excel::store(new catalog_location_export($this->filter), $path, 'local', MaatwebsiteExcel::MPDF) : Excel::store(new catalog_location_export($this->filter), $path, 'local');

        if ($file) {
            // update table report
            $executionTime = microtime(true) - $this->startTime;
            report_file::where('id', $this->idfile)->update([
                'path'          => $path,
                'execute_time'  => number_format($executionTime, 2, ',', '.'),
                'status'        => 1
            ]);

            removeFiles::removeJobTempFiles();
        }
    }

    public function failed(Throwable $exception)
    {
        $executionTime = microtime(true) - $this->startTime;
        report_file::where('id', $this->idfile)->update([
            'execute_time'  => number_format($executionTime, 2, ',', '.'),
            'status'        => 2,
            'exception'     => Str::limit($exception->getMessage(), 225, '')
        ]);
    }
}
