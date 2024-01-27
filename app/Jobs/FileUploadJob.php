<?php

namespace App\Jobs;

use App\Events\PusherEvent;
use App\Imports\EventImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class FileUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filePath)
    {
        //excel file
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     *
     * @return array|string[]
     */
    public function handle()
    {

        //processing starts
        try {

            $import = new EventImport;
            Excel::import($import, $this->filePath); //Excel file import starts

            $importedRows = $import->getRowCount(); // Get the count of successfully imported rows

            $response = [
                'status' => 'success',
                'msg' => 'File uploaded successfully',
                'data' => "<strong>" . $importedRows . "</strong> Rows added",
            ];

        } catch (ValidationException $e) {
            //Error Handling
            $failures = $e->failures();
            $all_errors = $e->errors();
            $errormessage = '';

            //Get the error's row position and msg
            foreach ($failures as $failure) {
                $err = implode('', $failure->errors());
                $errormessage .= " At Row <strong>" . ($failure->row() + 1) . "</strong>, ";
                $errormessage .= "<span>" . $err . "</span>";
                $errormessage .= " where values are " . json_encode(array_values($failure->values()));
                $errormessage .= "<br>\n";
            }

            $response = [
                'status' => 'error',
                'msg' => 'Some Error Occurred',
                'data' => $errormessage,
            ];

        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'msg' => 'Some Error Occurred',
                'data' => $e->getMessage(),
            ];
        }

        //processing ends here


        //notify the client
        $this->broadcastNotification($response);
    }

    protected function broadcastNotification($data): void
    {
        //hit the notification
        event(new PusherEvent($data));
    }

}
