<?php

namespace App\Http\Controllers;

use App\Helper\FileHandler;
use App\Http\Resources\ApiResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ApiResource
     */
    public function index()
    {
        $filter = request()->filter; //query parameter
        $events = Event::filterEvent($filter)->orderBy('start_date')->get();

        return new ApiResource($events);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return ApiResource
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'image' => 'required|image|max:2048',
        ]);

        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        //event created successfully

        if ($request->hasFile('image')) {
            //If request contains file
            $imageFile = $request->file('image');
            $uploadedPath = FileHandler::upload($imageFile, 'event_images');
            if ($uploadedPath) {
                //file moved and saving to db
                $event->update('image', $uploadedPath);
            }
        }

        return new ApiResource(['status' => 'success', 'msg' => $event->title . ' added successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param Event $event
     * @return ApiResource
     */
    public function show(Event $event)
    {
        return new ApiResource($event);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Event $event
     * @return ApiResource
     */
    public function update(Request $request, Event $event): ApiResource
    {
        $request->validate([
            'image' => 'nullable|image|max:2048',
        ]);

        $event = Event::findOrFail($id)->update([
            $request->except('image')
        ]);

        if ($request->hasFile('image')) {
            //If request contains file
            $imageFile = $request->file('image');
            $uploadedPath = FileHandler::upload($imageFile, 'event_images');
            if ($uploadedPath) {
                /*file moved successfully */

                //deleting existing old file
                FileHandler::delete($event->image);
                //replace new path with old one
                $event->update('image', $uploadedPath);
            }
        }

        return new ApiResource(['status' => 'success', 'msg' => $event->title . ' updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Event $event
     * @return ApiResource
     */
    public function destroy(Event $event)
    {
        $event->update('deleted_at', now());

        return new ApiResource(['status' => 'success', 'msg' => $event->title . ' deleted successfully']);
    }
}
