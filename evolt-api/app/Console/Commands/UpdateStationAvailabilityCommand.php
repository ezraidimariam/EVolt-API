<?php

namespace App\Console\Commands;

use App\Jobs\UpdateStationAvailability;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateStationAvailabilityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stations:update-availability';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update station availability based on reservation expiration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting station availability update...');
        
        try {
            // Dispatch the job to update availability
            UpdateStationAvailability::dispatch();
            
            $this->info('Station availability update job dispatched successfully!');
            Log::info('Station availability update command executed');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error updating station availability: ' . $e->getMessage());
            Log::error('Station availability update command failed: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}
