<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Tampilkan halaman Kontak Kami.
     */
    public function index()
    {
        $contactData = [
            'whatsapp' => '085282784356',
            'whatsapp_link' => 'https://wa.me/6285282784356?text=' . rawurlencode('Halo Admin URSKYND, saya ingin bertanya tentang layanan dan produk URSKYND.'),
            'email' => 'support@urskynd.com',
            'address' => 'Jl. Raya Plalangan Plosowahyu Km. 3, Kec. Lamongan, Kab. Lamongan, Jawa Timur 62218 (Kampus UMLA)',
            'hours' => 'Senin - Minggu (08.00 - 21.00 WIB)',
        ];

        return view('contact.index', compact('contactData'));
    }

    /**
     * Kirim pesan kontak dari formulir.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'contact' => 'required|string|max:100', // email atau nomor whatsapp
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:1000',
        ]);

        // Jika user mencentang untuk langsung chat WhatsApp
        if ($request->boolean('redirect_wa')) {
            $msg = "Halo Admin URSKYND,\n\n"
                 . "*Pesan dari Form Kontak Website*:\n"
                 . "👤 Nama: " . $validated['name'] . "\n"
                 . "📞 Kontak: " . $validated['contact'] . "\n"
                 . "📌 Subjek: " . $validated['subject'] . "\n"
                 . "💬 Pesan: " . $validated['message'];

            return redirect('https://wa.me/6285282784356?text=' . rawurlencode($msg));
        }

        return redirect()->route('contact.index')->with('success', 'Pesan Anda berhasil terkirim! Tim Customer Support URSKYND akan segera menghubungi Anda.');
    }
}
