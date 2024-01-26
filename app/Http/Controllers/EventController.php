<?php

namespace App\Http\Controllers;

use App\Helper\FileHandler;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //query parameter
        $filter = request()->filter;
        $keyword = request()->keyword;

        //query builder
        $events = Event::filterEvent($filter)
            ->when(isset($keyword), function ($query) use($keyword){
                $query->where(function ($query) use($keyword) {
                    $query->orWhere('title','LIKE','%'.$keyword.'%')
                        ->orWhere('description','LIKE','%'.$keyword.'%');
                });
            })
            ->orderBy('start_date')
            ->get();

        //return events
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
        //Validating the Request
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'required|image|max:2048',
        ]);

        //Handle file uploading
        $imageFile = $request->file('image');
        $uploadedPath = FileHandler::upload($imageFile, 'event_images');

        if (!$uploadedPath) {
            //File couldn't upload
            return response()->json(['status' => 'error', 'msg' => 'Something went wrong!']);
        }

        //New Event creation
        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'image' => $uploadedPath
        ]);

        //Event created successfully :)
        return response()->json(['status' => 'success', 'msg' => $event->title . ' added successfully']);
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
            'image' => 'nullable|image|max:2048',
        ]);

        $event = Event::findOrFail($id);
        $event->update($request->except('image'));

        if ($request->hasFile('image')) {
            //If request contains file
            $imageFile = $request->file('image');
            $uploadedPath = FileHandler::upload($imageFile, 'event_images');

            /*File moved successfully */
            if ($uploadedPath) {
                //deleting existing old file
                FileHandler::delete($event->image);
                //replace new path with old one
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
        $event->deleted_at = now();
        $event->save();
        return response()->json(['status' => 'success', 'msg' => $event->title . ' deleted successfully']);
    }
}
