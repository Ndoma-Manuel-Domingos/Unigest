<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::query()
            ->when(
                $request->filled('search'),
                fn ($query) =>
                    $query->where(function ($q) use ($request) {
                        $search = $request->search;

                        $q->where('name', 'ILIKE', "%{$search}%")
                            ->orWhere('code', 'ILIKE', "%{$search}%")
                            ->orWhere('building', 'ILIKE', "%{$search}%");
                    })
            )
            ->when(
                $request->filled('status'),
                fn ($query) =>
                    $query->where(
                        'is_active',
                        $request->status === 'active'
                    )
            )
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'university.rooms.index',
            compact('rooms')
        );
    }

    public function create()
    {
        return view('university.rooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'building' => [
                'nullable',
                'string',
                'max:255',
            ],

            'floor' => [
                'nullable',
                'string',
                'max:50',
            ],

            'type' => [
                'required',
                'in:classroom,laboratory,auditorium,office,other',
            ],

            'capacity' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['is_active'] =
            $request->boolean('is_active');

        Room::create($validated);

        return redirect()
            ->route('university.rooms.index')
            ->with('success', 'Sala criada com sucesso.');
    }

    public function edit(string $room)
    {
        $room = Room::findOrFail($room);

        return view(
            'university.rooms.edit',
            compact('room')
        );
    }

    public function update(
        Request $request,
        string $room
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'building' => [
                'nullable',
                'string',
                'max:255',
            ],

            'floor' => [
                'nullable',
                'string',
                'max:50',
            ],

            'type' => [
                'required',
                'in:classroom,laboratory,auditorium,office,other',
            ],

            'capacity' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $room = Room::findOrFail($room);
        $room->update($validated);

        return redirect()
            ->route('university.rooms.index')
            ->with('success', 'Sala actualizada com sucesso.');
    }

    public function destroy(Room $room)
    {
        if ($room->schedules()->exists()) {
            return back()->with(
                'error',
                'Não é possível eliminar uma sala que possui horários.'
            );
        }

        $room->delete();

        return redirect()
            ->route('university.rooms.index')
            ->with('success', 'Sala eliminada com sucesso.');
    }
}
