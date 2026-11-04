<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    /**
     * Display the consultation directory & booking page.
     */
    public function index()
    {
        $doctors = \App\Models\Doctor::orderByDesc('available')->orderBy('id')->get();

        if ($doctors->isEmpty()) {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'DoctorSeeder']);
            $doctors = \App\Models\Doctor::orderByDesc('available')->orderBy('id')->get();
        }

        $concerns = [
            'Jerawat aktif & komedo membandel',
            'Skin barrier rusak & kemerahan',
            'Flek hitam / bekas jerawat PIH/PIE',
            'Kulit kusam & tekstur kasar',
            'Kerutan halus & tanda penuaan dini',
            'Kulit sangat kering & mengelupas',
            'Rekomendasi pemilihan produk skincare',
        ];

        return view('consultation.index', compact('doctors', 'concerns'));
    }

    /**
     * Handle consultation form submission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'nullable',
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:25',
            'concern' => 'required|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        $doctor = null;
        if (!empty($validated['doctor_id'])) {
            $doctor = \App\Models\Doctor::find($validated['doctor_id']);
        }

        // Simpan booking ke database untuk riwayat admin
        try {
            \App\Models\ConsultationBooking::create([
                'doctor_id' => $doctor?->id,
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'concern' => $validated['concern'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);
        } catch (\Throwable $e) {
            // Ignore if booking table fails, continue to WA
        }

        return redirect()->route('consultation.index')->with('success', 'Permohonan konsultasi Anda berhasil dikirim! Tim dokter dan formulator URSKYND akan segera menghubungi Anda.');
    }
}
