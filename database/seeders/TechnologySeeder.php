<?php

namespace Database\Seeders;

use App\Models\Technology;
use Illuminate\Database\Seeder;

class TechnologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $technologies = [
            ['name' => 'PHP',          'icon' => 'php'],
            ['name' => 'Laravel',      'icon' => 'laravel'],
            ['name' => 'JavaScript',   'icon' => 'javascript'],
            ['name' => 'TypeScript',   'icon' => 'typescript'],
            ['name' => 'Vue.js',       'icon' => 'vuejs'],
            ['name' => 'React',        'icon' => 'react'],
            ['name' => 'Node.js',      'icon' => 'nodejs'],
            ['name' => 'Python',       'icon' => 'python'],
            ['name' => 'PostgreSQL',   'icon' => 'postgresql'],
            ['name' => 'MySQL',        'icon' => 'mysql'],
            ['name' => 'Docker',       'icon' => 'docker'],
            ['name' => 'Git',          'icon' => 'git'],
            ['name' => 'HTML',         'icon' => 'html5'],
            ['name' => 'CSS',          'icon' => 'css3'],
            ['name' => 'Tailwind CSS', 'icon' => 'tailwindcss'],
            ['name' => 'Bootstrap',    'icon' => 'bootstrap'],
            ['name' => 'REST API',     'icon' => 'api'],
            ['name' => 'GraphQL',      'icon' => 'graphql'],
            ['name' => 'Redis',        'icon' => 'redis'],
            ['name' => 'Linux',        'icon' => 'linux'],
        ];

        foreach ($technologies as $tech) {
            Technology::firstOrCreate(['name' => $tech['name']], $tech);
        }
    }
}
