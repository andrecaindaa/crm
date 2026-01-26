<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entity;
use Inertia\Inertia;

class EntityController extends Controller
{
    public function index()
    {
        $entities = Entity::where('user_id', auth()->id())
            ->latest()
            ->get();

        return Inertia::render('Entities/Index', [
            'entities' => $entities,
        ]);
    }

    public function create()
    {
        return Inertia::render('Entities/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'vat' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'status' => 'required|string',
        ]);

        Entity::create([
            ...$data,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('entities.index');
    }

    public function edit(Entity $entity)
    {
        $this->authorize('update', $entity);

        return Inertia::render('Entities/Edit', [
            'entity' => $entity,
        ]);
    }

    public function update(Request $request, Entity $entity)
    {
        $this->authorize('update', $entity);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'vat' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $entity->update($data);

        return redirect()->route('entities.index');
    }

    public function destroy(Entity $entity)
    {
        $this->authorize('delete', $entity);

        $entity->delete();

        return redirect()->route('entities.index');
    }
}
