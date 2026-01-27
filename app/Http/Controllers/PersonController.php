<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Person;
use App\Models\Entity;
use Inertia\Inertia;

class PersonController extends Controller
{
    public function index()
    {
        $people = Person::with('entity')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return Inertia::render('People/Index', [
            'people' => $people,
        ]);
    }

    public function create()
    {
        return Inertia::render('People/Create', [
            'entities' => Entity::where('user_id', auth()->id())->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'entity_id' => 'nullable|exists:entities,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        Person::create([
            ...$data,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('people.index');
    }

    public function edit(Person $person)
    {
        $this->authorize('update', $person);

        return Inertia::render('People/Edit', [
            'person' => $person,
            'entities' => Entity::where('user_id', auth()->id())->get(),
        ]);
    }

    public function update(Request $request, Person $person)
    {
        $this->authorize('update', $person);

        $data = $request->validate([
            'entity_id' => 'nullable|exists:entities,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $person->update($data);

        return redirect()->route('people.index');
    }

    public function destroy(Person $person)
    {
        $this->authorize('delete', $person);

        $person->delete();

        return redirect()->route('people.index');
    }
}
