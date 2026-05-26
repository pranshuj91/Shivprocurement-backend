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
                'password' => 'password', // hashed automatically by cast in User model
                'role' => 'manager',
            ]
        );

        User::updateOrCreate(
            ['phone' => '9829012345'],
            [
                'name' => 'Field Supervisor',
                'pin' => '1234', // hashed automatically by cast in User model
                'role' => 'supervisor',
            ]
        );

        User::updateOrCreate(
            ['email' => 'lab@shivedibles.com'],
            [
                'name' => 'Lab Technician',
                'password' => 'password',
                'role' => 'lab',
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

        // 4. Seed Unloading Entries and Media Logs for dashboard testing
        $dbUnits = Unit::all();
        $dbSuppliers = Supplier::all();
        $statuses = ['pending', 'approved', 'flagged', 'rejected'];
        $purchaseTypes = ['Depo', 'Direct', 'Other'];

        for ($i = 1; $i <= 30; $i++) {
            $unit = $dbUnits->random();
            $supplierName = $dbSuppliers->random()->name;
            $purchaseType = $purchaseTypes[array_rand($purchaseTypes)];
            
            $stateCodes = ['RJ', 'MP', 'UP', 'DL'];
            $plate = $stateCodes[array_rand($stateCodes)] . '-' . rand(10, 99) . '-' . chr(rand(65, 90)) . chr(rand(65, 90)) . '-' . rand(1000, 9999);

            $outOfSpec = ($i % 5 === 0); // 20% out of spec
            if ($outOfSpec) {
                $moisture = rand(101, 150) / 10.0;
                $fm = rand(21, 50) / 10.0;
                $dm = rand(21, 40) / 10.0;
                $status = 'flagged';
            } else {
                $moisture = rand(60, 100) / 10.0;
                $fm = rand(5, 20) / 10.0;
                $dm = rand(5, 20) / 10.0;
                $status = $statuses[array_rand(['pending', 'approved', 'approved', 'rejected'])];
            }

            $latOffset = (rand(-100, 100) / 10000.0);
            $lngOffset = (rand(-100, 100) / 10000.0);
            
            $gross = rand(22000, 45000) / 1000.0;
            $tare = rand(8000, 14000) / 1000.0;
            $net = $gross - $tare;
            
            $operators = ['Rajesh Kumar', 'S. Sharma', 'Amit Patel', 'Vikram Singh'];
            $operator = $operators[array_rand($operators)];
            
            $remarksList = [
                'approved' => [
                    'All specifications within standard limits.',
                    'Verified and approved.',
                    'Quality check complete. Moisture in limit.'
                ],
                'flagged' => [
                    "Moisture is {$moisture}%, exceeding threshold of 10.0%. Rebate deductions applied.",
                    "High Foreign Matter: {$fm}%. Flagged for procurement discount.",
                    "Damaged seeds count {$dm}% is high. Flagged for manager review."
                ],
                'rejected' => [
                    "Rejected: Moisture of {$moisture}% exceeds maximum permissible limit.",
                    "Rejected due to heavy stones and mud contamination.",
                    "Rejected: Material contains damaged seeds {$dm}% exceeding limit."
                ],
                'pending' => [
                    'Awaiting quality lab validation.',
                    'Truck weighed. Material inspection in progress.'
                ]
            ];
            
            $remarks = $remarksList[$status][array_rand($remarksList[$status])];

            $entry = \App\Models\UnloadingEntry::create([
                'id' => 'UL-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'unit_id' => $unit->id,
                'truck_no' => $plate,
                'purchase_type' => $purchaseType,
                'sourced_from' => $supplierName,
                'moisture' => $moisture,
                'fm' => $fm,
                'dm' => $dm,
                'status' => $status,
                'latitude' => $unit->latitude + $latOffset,
                'longitude' => $unit->longitude + $lngOffset,
                'gps_accuracy' => rand(3, 15),
                'gross_weight' => $gross,
                'tare_weight' => $tare,
                'net_weight' => $net,
                'remarks' => $remarks,
                'operator_name' => $operator,
                'created_at' => now()->subDays(rand(0, 15))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
            ]);

            \App\Models\MediaLog::create([
                'unloading_entry_id' => $entry->id,
                'type' => 'truck',
                'file_path' => '/images/mock_truck.png',
                'caption' => 'Weighbridge capture for ' . $plate,
            ]);

            \App\Models\MediaLog::create([
                'unloading_entry_id' => $entry->id,
                'type' => 'material',
                'file_path' => '/images/mock_material.png',
                'caption' => 'Soybean quality sample moisture check',
            ]);

            if ($i % 2 === 0) {
                \App\Models\MediaLog::create([
                    'unloading_entry_id' => $entry->id,
                    'type' => 'audio',
                    'file_path' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
                    'caption' => 'Supervisor audio note on crop quality',
                ]);
            }
        }
    }
}
