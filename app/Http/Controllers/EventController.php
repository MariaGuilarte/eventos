<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $events = Event::all();
        return Inertia::render('Events/CreateEvent', compact('events'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['title'=>'required', 'start'=>'required', 'end'=>'required']);
        $event = Event::create($request->only(['title', 'start', 'end', 'doctor_name']));
        return to_route('events.create', ['event'=>$event]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $request->validate(['title'=>'required', 'start'=>'required', 'end'=>'required']);
        $event->update($request->only(['title', 'start', 'end', 'doctor_name']));
        return to_route('events.create', ['event'=>$event]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return to_route('events.create', ['event'=>$event]);
    }
}
