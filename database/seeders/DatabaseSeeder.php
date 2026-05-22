<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Unit;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Manager/Supervisor User for testing API authentication
        User::updateOrCreate(
            ['email' => 'admin@shivedibles.com'],
            [
                'name' => 'Admin Manager',
                'password' => bcrypt('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'supervisor@shivedibles.com'],
            [
                'name' => 'Field Supervisor',
                'password' => bcrypt('password'),
            ]
        );

        // 2. Seed crushing units
        $units = [
            [
                'name' => 'Shiv Agrevo Ltd., Baran',
                'code' => 'BARAN',
                'latitude' => 25.10110000,
                'longitude' => 76.51110000,
            ],
            [
                'name' => 'Shiv Edibles Ltd., Ranpur, Kota',
                'code' => 'RANPUR',
                'latitude' => 25.07440000,
                'longitude' => 75.83720000,
            ],
            [
                'name' => 'Shiv Edibles Ltd, Unit II, Moondla, Kota',
                'code' => 'MOONDLA',
                'latitude' => 25.13840000,
                'longitude' => 75.90120000,
            ],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(['code' => $unit['code']], $unit);
        }

        // 3. Seed 50 Suppliers (Mandis & Traders)
        $suppliers = [
            'Baran Mandi',
            'Kota Mandi',
            'Bundi Mandi',
            'Jhalawar Mandi',
            'Anta Mandi',
            'Itawa Mandi',
            'Sangod Mandi',
            'Ramganj Mandi',
            'Chhipabarod Mandi',
            'Atru Mandi',
            'Mangrol Mandi',
            'Kawai Mandi',
            'Khanpur Mandi',
            'Bakani Mandi',
            'Aklera Mandi',
            'Manohar Thana Mandi',
            'Bhawani Mandi',
            'Sunel Mandi',
            'Pirawa Mandi',
            'Siswali Mandi',
            'Sultanpur Mandi',
            'Kaithoon Mandi',
            'Keshoraipatan Mandi',
            'Indargarh Mandi',
            'Nainwa Mandi',
            'Lakheri Mandi',
            'Hindoli Mandi',
            'Dei Mandi',
            'Talera Mandi',
            'Khatoli Mandi',
            'Shiv Agro Agencies',
            'Rajasthan Soya Traders',
            'Hadoti Seed Corporation',
            'Chambal Grain Merchants',
            'Kota Seed Suppliers',
            'Baran Soya Syndicate',
            'Shrinath Agro Industries',
            'Balaji Grain Traders',
            'Mahaveer Seed Agency',
            'Karni Enterprises',
            'Dev Soya Traders',
            'Radhey Shyam & Sons',
            'Krishna Agro Trading',
            'Gopal Seed Traders',
            'Om Soya Marketing',
            'R.K. Grain Suppliers',
            'Vardhaman Soya Agency',
            'Vinayak Agro Products',
            'Maruti Seed Traders',
            'Bajrang Seed Corporation',
        ];

        foreach ($suppliers as $name) {
            Supplier::updateOrCreate(['name' => $name]);
        }
    }
}
