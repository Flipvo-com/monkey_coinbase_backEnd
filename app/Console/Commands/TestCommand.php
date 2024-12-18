<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-command';
//    protected $signature = 'app:test-command {argument} {--option}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test stuff';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Log::info('Test command executed');
        $this->info('Command executed successfully!');
    }
}
