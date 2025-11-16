<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use App\Models\DeviceUser;
use App\Models\PublicRequest;
use App\Models\ApprovedVisitor;
use App\Models\GateLog;
use App\Models\User;

class VisitorManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@vms.local'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create devices
        $devices = [
            [
                'device_id' => 'GATE-MAIN-001',
                'name' => 'Main Gate',
                'location' => 'Front Entrance',
                'device_type' => 'gate',
                'status' => 'active',
                'ip_address' => '192.168.1.100',
                'firmware_version' => '1.0.0',
            ],
            [
                'device_id' => 'GATE-SIDE-001',
                'name' => 'Side Gate',
                'location' => 'Side Entrance',
                'device_type' => 'gate',
                'status' => 'active',
                'ip_address' => '192.168.1.101',
                'firmware_version' => '1.0.0',
            ],
            [
                'device_id' => 'TURNSTILE-LOBBY-001',
                'name' => 'Lobby Turnstile',
                'location' => 'Main Lobby',
                'device_type' => 'turnstile',
                'status' => 'active',
                'ip_address' => '192.168.1.102',
                'firmware_version' => '2.0.0',
            ],
        ];

        $createdDevices = [];
        foreach ($devices as $deviceData) {
            $device = Device::firstOrCreate(
                ['device_id' => $deviceData['device_id']],
                $deviceData
            );
            $createdDevices[] = $device;
        }

        // Create sample device users
        foreach ($createdDevices as $device) {
            DeviceUser::firstOrCreate(
                [
                    'device_id' => $device->id,
                    'external_user_id' => 'EMP-001',
                ],
                [
                    'name' => 'John Employee',
                    'id_proof' => 'CARD-001',
                    'access_level' => 'employee',
                    'status' => 'active',
                    'valid_from' => now()->toDateString(),
                ]
            );
        }

        // Create sample public requests
        $requests = [
            [
                'visitor_name' => 'Rajesh Kumar',
                'visitor_email' => 'rajesh@example.com',
                'visitor_phone' => '+91-9876543210',
                'id_proof_type' => 'passport',
                'id_proof_number' => 'ABC123456',
                'purpose_of_visit' => 'Business Meeting',
                'visit_date' => now()->addDays(1)->toDateString(),
                'visit_time' => '10:00',
                'host_department' => 'Sales',
                'host_person_name' => 'John Doe',
                'status' => 'pending',
            ],
            [
                'visitor_name' => 'Priya Singh',
                'visitor_email' => 'priya@example.com',
                'visitor_phone' => '+91-8765432109',
                'id_proof_type' => 'aadhaar',
                'id_proof_number' => 'AADHAAR123',
                'purpose_of_visit' => 'Client Visit',
                'visit_date' => now()->toDateString(),
                'visit_time' => '14:30',
                'host_department' => 'Operations',
                'host_person_name' => 'Jane Smith',
                'status' => 'approved',
                'approved_by' => $adminUser->id,
                'approved_at' => now(),
            ],
        ];

        foreach ($requests as $requestData) {
            $request = PublicRequest::firstOrCreate(
                ['visitor_phone' => $requestData['visitor_phone']],
                $requestData
            );

            // Create approved visitor for approved requests
            if ($request->status === 'approved' && !$request->approvedVisitor) {
                ApprovedVisitor::create([
                    'public_request_id' => $request->id,
                    'visitor_name' => $request->visitor_name,
                    'visitor_phone' => $request->visitor_phone,
                    'id_proof_type' => $request->id_proof_type,
                    'id_proof_number' => $request->id_proof_number,
                    'purpose_of_visit' => $request->purpose_of_visit,
                    'visit_date' => $request->visit_date,
                    'visit_time' => $request->visit_time,
                    'host_department' => $request->host_department,
                    'host_person_name' => $request->host_person_name,
                    'badge_number' => 'VMS-PRIYA001',
                    'status' => 'active',
                    'metadata' => [
                        'access_level' => 'visitor',
                        'entry_point' => 'main_gate',
                    ],
                ]);
            }
        }

        // Create sample gate logs
        $approvedVisitor = ApprovedVisitor::first();
        if ($approvedVisitor && $approvedVisitor->visit_date <= now()->toDateString()) {
            $mainGate = Device::where('device_id', 'GATE-MAIN-001')->first();

            GateLog::firstOrCreate(
                [
                    'approved_visitor_id' => $approvedVisitor->id,
                    'device_id' => $mainGate->id,
                    'event_type' => 'checkin',
                ],
                [
                    'visitor_name' => $approvedVisitor->visitor_name,
                    'badge_number' => $approvedVisitor->badge_number,
                    'event_at' => now()->subHours(2),
                    'ip_address' => '192.168.1.100',
                ]
            );

            GateLog::firstOrCreate(
                [
                    'approved_visitor_id' => $approvedVisitor->id,
                    'device_id' => $mainGate->id,
                    'event_type' => 'checkout',
                ],
                [
                    'visitor_name' => $approvedVisitor->visitor_name,
                    'badge_number' => $approvedVisitor->badge_number,
                    'event_at' => now()->subMinutes(30),
                    'ip_address' => '192.168.1.100',
                ]
            );
        }

        $this->command->info('VMS seeder completed successfully!');
    }
}
