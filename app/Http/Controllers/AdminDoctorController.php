<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\ConsultationBooking;
use Illuminate\Http\Request;

class AdminDoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::withCount('bookings')->latest()->get();
        $bookings = ConsultationBooking::with('doctor')->latest()->paginate(15);

        return view('admin.doctors.index', compact('doctors', 'bookings'));
    }

    public function create()
    {
        return view('admin.doctors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'title' => 'required|string|max:150',
            'experience' => 'required|string|max:100',
            'hospital' => 'required|string|max:150',
            'rating' => 'required|string|max:10',
            'specialties' => 'required|string',
            'price' => 'required|string|max:100',
            'avatar' => 'nullable|string|max:500',
            'whatsapp_number' => 'nullable|string|max:30',
            'available' => 'nullable|boolean',
        ]);

        $validated['available'] = $request->has('available') ? $request->boolean('available') : false;

        Doctor::create($validated);

        return redirect()->route('admin.doctors.index')->with('success', 'Dokter konsultasi berhasil ditambahkan!');
    }

    public function edit(Doctor $doctor)
    {
        return view('admin.doctors.edit', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'title' => 'required|string|max:150',
            'experience' => 'required|string|max:100',
            'hospital' => 'required|string|max:150',
            'rating' => 'required|string|max:10',
            'specialties' => 'required|string',
            'price' => 'required|string|max:100',
            'avatar' => 'nullable|string|max:500',
            'whatsapp_number' => 'nullable|string|max:30',
            'available' => 'nullable|boolean',
        ]);

        $validated['available'] = $request->has('available') ? $request->boolean('available') : false;

        $doctor->update($validated);

        return redirect()->route('admin.doctors.index')->with('success', 'Data dokter berhasil diperbarui!');
    }

    public function toggleAvailability(Request $request, Doctor $doctor)
    {
        $newStatus = $request->has('available') ? $request->boolean('available') : !$doctor->available;
        $doctor->update(['available' => $newStatus]);

        $label = $newStatus ? 'Siap Melayani Konsultasi' : 'Segera Hadir (Jadwal Penuh)';

        return back()->with('success', 'Status layanan ' . $doctor->name . ' berhasil diubah menjadi: ' . $label . '.');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();
        return redirect()->route('admin.doctors.index')->with('success', 'Data dokter berhasil dihapus.');
    }

    public function updateBooking(Request $request, ConsultationBooking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,contacted,completed',
        ]);

        $booking->update($validated);

        return back()->with('success', 'Status permohonan konsultasi diperbarui.');
    }
}
