<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = [
            [
                'name' => 'dr. Amanda Sp.KK, FINSDV',
                'title' => 'Spesialis Dermatologi & Venereologi',
                'experience' => '8 Tahun Pengalaman',
                'hospital' => 'Klinik Utama Kulit & Estetika URSKYND',
                'rating' => '4.9',
                'reviews_count' => 142,
                'specialties' => 'Jerawat Parah (Cystic), Skin Barrier Rusak, Eksim & Dermatitis',
                'available' => true,
                'price' => 'Gratis via URSKYND Support',
                'avatar' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&q=80&w=300',
            ],
            [
                'name' => 'dr. Reza Pratama Sp.DV',
                'title' => 'Spesialis Kulit & Kelamin, Anti-Aging',
                'experience' => '10 Tahun Pengalaman',
                'hospital' => 'RS Siloam & Partner Medis URSKYND',
                'rating' => '5.0',
                'reviews_count' => 98,
                'specialties' => 'Anti-Aging & Kerutan, Hiperpigmentasi & Melasma, Pori-pori Besar',
                'available' => true,
                'price' => 'Gratis via URSKYND Support',
                'avatar' => 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=300',
            ],
            [
                'name' => 'dr. Sarah Wijaya Sp.KK',
                'title' => 'Dermatolog & Photobiology Specialist',
                'experience' => '6 Tahun Pengalaman',
                'hospital' => 'Skin Health Institute Jakarta',
                'rating' => '4.8',
                'reviews_count' => 115,
                'specialties' => 'Sun Damaged Skin, Kulit Sangat Sensitif, Rosacea',
                'available' => true,
                'price' => 'Gratis via URSKYND Support',
                'avatar' => 'https://images.unsplash.com/photo-1594824813515-307525287e07?auto=format&fit=crop&q=80&w=300',
            ],
            [
                'name' => 'Apt. Nadia S.Farm',
                'title' => 'Beauty Advisor & Skincare Formulator',
                'experience' => '5 Tahun Pengalaman',
                'hospital' => 'URSKYND Skincare Laboratory',
                'rating' => '4.9',
                'reviews_count' => 210,
                'specialties' => 'Kombinasi Bahan Aktif, Panduan Basic Skincare, Layering Serum',
                'available' => true,
                'price' => 'Gratis via Chat',
                'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=300',
            ],
        ];

        foreach ($doctors as $doc) {
            Doctor::updateOrCreate(
                ['name' => $doc['name']],
                $doc
            );
        }
    }
}
