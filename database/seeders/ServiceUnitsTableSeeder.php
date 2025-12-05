<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceUnit;
use Illuminate\Support\Str;

class ServiceUnitsTableSeeder extends Seeder
{
    public function run(): void
    {
        // canonical list (duplicates removed)
        $units = [
            ['name' => 'Choir',           'description' => 'Responsible for worship and music'],
            ['name' => 'Ushering',        'description' => 'Helps organize seating and church order'],
            ['name' => 'Media',           'description' => 'Handles sound, projection, and online media'],
            ['name' => 'Technical',       'description' => 'Responsible for equipment and systems'],
            ['name' => 'Protocol',        'description' => 'Manages ceremonies and special events'],
            ['name' => 'Call Center',     'description' => 'Make follow-ups calls to members'],
            ['name' => 'Prayer Band',     'description' => 'Leads intercessory prayer sessions'],
            ['name' => 'Security',        'description' => 'Ensures safety and security of members'],
            ['name' => 'Hospitality',     'description' => 'Welcomes and assists visitors and members'],
            ['name' => 'Transport',       'description' => 'Manages transportation for church activities'],
            ['name' => 'Crowd Control',   'description' => 'Manages large gatherings and events'],
            ['name' => 'Sanctuary',       'description' => 'Maintains cleanliness and order of the sanctuary'],
            ['name' => 'Sunday School',   'description' => 'Conducts religious education for children'],
        ];

        foreach ($units as $unit) {
            // Normalize and guard against accidental duplicates/casing
            $name = trim($unit['name']);

            ServiceUnit::updateOrCreate(
                // match by name (so repeated runs won't create duplicates)
                ['name' => $name],
                // values to insert / update
                [
                    'description' => $unit['description'] ?? null,
                    // add other fields here if your model has them, e.g. 'slug' => Str::slug($name)
                ]
            );
        }
    }
}
