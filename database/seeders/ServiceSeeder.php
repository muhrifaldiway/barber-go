<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Potong Rambut',
                'description' => 'Layanan potong rambut profesional',
                'icon' => 'scissors',
                'services' => [
                    ['name' => 'Potong Rambut Standar', 'price' => 50000, 'duration_minutes' => 30, 'description' => 'Potong rambut standar dengan style terkini'],
                    ['name' => 'Potong Rambut Premium', 'price' => 75000, 'duration_minutes' => 45, 'description' => 'Potong rambut premium dengan konsultasi style'],
                    ['name' => 'Potong Rambut Anak', 'price' => 35000, 'duration_minutes' => 20, 'description' => 'Potong rambut untuk anak-anak'],
                ],
            ],
            [
                'name' => 'Cukur & Shaving',
                'description' => 'Layanan cukur kumis, jenggot, dan shaving',
                'icon' => 'razor',
                'services' => [
                    ['name' => 'Cukur Kumis & Jenggot', 'price' => 25000, 'duration_minutes' => 15, 'description' => 'Rapihkan kumis dan jenggot'],
                    ['name' => 'Hot Towel Shave', 'price' => 45000, 'duration_minutes' => 30, 'description' => 'Classic hot towel shave yang mewah'],
                    ['name' => 'Beard Trim & Shape', 'price' => 35000, 'duration_minutes' => 20, 'description' => 'Trim dan bentuk jenggot sesuai wajah'],
                ],
            ],
            [
                'name' => 'Hair Treatment',
                'description' => 'Perawatan rambut',
                'icon' => 'spa',
                'services' => [
                    ['name' => 'Hair Wash & Blow', 'price' => 30000, 'duration_minutes' => 20, 'description' => 'Cuci rambut dan blow dry'],
                    ['name' => 'Creambath', 'price' => 60000, 'duration_minutes' => 45, 'description' => 'Creambath untuk nutrisi rambut'],
                    ['name' => 'Hair Coloring', 'price' => 150000, 'duration_minutes' => 90, 'description' => 'Pewarnaan rambut profesional'],
                    ['name' => 'Hair Tonic Treatment', 'price' => 40000, 'duration_minutes' => 20, 'description' => 'Treatment tonik untuk rambut sehat'],
                ],
            ],
            [
                'name' => 'Paket Kombo',
                'description' => 'Paket layanan hemat',
                'icon' => 'package',
                'services' => [
                    ['name' => 'Paket Hemat (Potong + Cuci)', 'price' => 65000, 'duration_minutes' => 45, 'description' => 'Potong rambut + cuci rambut'],
                    ['name' => 'Paket Sultan (Potong + Shave + Creambath)', 'price' => 120000, 'duration_minutes' => 90, 'description' => 'Paket lengkap premium'],
                    ['name' => 'Paket Grooming (Potong + Cukur + Hair Tonic)', 'price' => 100000, 'duration_minutes' => 60, 'description' => 'Paket grooming lengkap'],
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $services = $catData['services'];
            unset($catData['services']);

            $category = ServiceCategory::create($catData);

            foreach ($services as $serviceData) {
                $serviceData['category_id'] = $category->id;
                Service::create($serviceData);
            }
        }
    }
}