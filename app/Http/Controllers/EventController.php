<?php

namespace App\Http\Controllers;

use App\Helper\FileHandler;
use App\Imports\EventImport;
use App\Models\Event;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // query parameter
        $filter = request()->filter;  //event filter
        $keyword = request()->keyword; //search
        $rows = request()->rows ?? 25; //pagination

        if ($rows == 'all') {
            $rows = HomeSlider::count();
        }

        // query builder
        $events = Event::filterEvent($filter)
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->orWhere('title', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('description', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->orderBy('start_date')
            ->paginate($rows);

        // return events
        return response()->json($events);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validating the Request
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'required|image|max:2048',
        ]);

        // Handle file uploading
        $imageFile = $request->file('image');
        $uploadedPath = FileHandler::upload($imageFile, 'event_images');

        if (!$uploadedPath) {
            // File couldn't upload
            return response()->json(['status' => 'error', 'msg' => 'Something went wrong!']);
        }

        // New Event creation
        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'image' => $uploadedPath
        ]);

        // Event created successfully :)
        return response()->json(['status' => 'success', 'msg' => $event->title . ' added successfully']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeBulkData(Request $request)
    {
        // Validating the Request
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:20000',
        ]);

        try {
            $import = new EventImport;
            Excel::import($import, request()->file('file')); //Excel file import starts

            $importedRows = $import->getRowCount(); // Get the count of successfully imported rows

            $response = [
                'status' => 'success',
                'msg' => 'File uploaded successfully',
                'data' => "<strong style='color: green;'>" . $importedRows . "</strong> Rows added",
            ];

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            //Error Handling
            $failures = $e->failures();
            $all_errors = $e->errors();
            $errormessage = '';

            //Get the row positionig and error
            foreach ($failures as $failure) {
                $err = implode('', $failure->errors());
                $errormessage .= " At Row <strong>" . ($failure->row() + 1) . "</strong>, ";
                $errormessage .= "<span style='color: red'>" . $err . "</span>";
                $errormessage .= " where values are " . json_encode(array_values($failure->values()));
                $errormessage .= "<br>\n";
            }

            $response = [
                'status' => 'error',
                'msg' => 'Some Error Occurred',
                'data' => $errormessage,
            ];
        }

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return $event = Event::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'nullable|image|max:2048',
        ]);

        $event = Event::findOrFail($id);
        $event->update($request->except('image'));

        // Check whether request contains file
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $uploadedPath = FileHandler::upload($imageFile, 'event_images');

            /* File moved successfully */
            if ($uploadedPath) {
                // deleting existing old file
                FileHandler::delete($event->image);
                // replace new path with old one
                $event->update(['image' => $uploadedPath]);
            }
        }
        return response()->json(['status' => 'success', 'msg' => $event->title . ' updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->deleted_at = now(); //softly deleting
        $event->save();
        return response()->json(['status' => 'success', 'msg' => $event->title . ' deleted successfully']);
    }
}
