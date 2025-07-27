<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\StudentClasses;

class FixClassNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:class-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix class_name data by converting JSON objects to plain strings';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to fix class_name data...');

        $users = User::whereNotNull('class_name')->get();
        $fixedCount = 0;

        foreach ($users as $user) {
            $className = $user->class_name;

            // Check if the class_name is a JSON object
            if (is_string($className) && (strpos($className, '{') === 0 || strpos($className, '[') === 0)) {
                $decoded = json_decode($className, true);

                if (is_array($decoded) && isset($decoded['class_name'])) {
                    // Extract the class_name from the JSON object
                    $user->class_name = $decoded['class_name'];
                    $user->save();
                    $fixedCount++;
                    $this->line("Fixed user {$user->name}: {$className} -> {$decoded['class_name']}");
                }
            }
        }

        $this->info("Fixed {$fixedCount} users with JSON class_name data.");
        $this->info('Class name fix completed!');

        return 0;
    }
}
