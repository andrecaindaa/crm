<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarEventController extends Controller
{
    public function index()
    {
        $events = CalendarEvent::where('owner_id', auth()->id())
            ->get()
            ->map(fn ($event) => [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_at,
                'end' => $event->end_at,
            ]);

        return Inertia::render('Calendar/Index', [
            'events' => $events,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', CalendarEvent::class);

        $data = $request->validate([
            'title' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date',
            'eventable_type' => 'nullable|string',
            'eventable_id' => 'nullable|integer',
        ]);

        CalendarEvent::create([
            ...$data,
            'owner_id' => auth()->id(),
        ]);

        return back();
    }
}
