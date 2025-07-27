<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestClassNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:class-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test class_name data to ensure it displays correctly';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Testing class_name data...');

        $users = User::whereNotNull('class_name')->get();

        if ($users->isEmpty()) {
            $this->info('No users with class_name found.');
            return 0;
        }

        $this->table(
            ['ID', 'Name', 'Class Name', 'Student Class ID'],
            $users->map(function ($user) {
                return [
                    $user->id,
                    $user->name,
                    $user->class_name,
                    $user->student_class_id
                ];
            })->toArray()
        );

        $this->info('Class name test completed!');
        return 0;
    }
}
