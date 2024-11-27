<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateLessonStatusesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Running UpdateLessonStatusesJob');
        // Get the current time
        $now = Carbon::now();
        // Update 'scheduled' lessons to 'cancelled'
//        $cancelledInstances = LessonInstance::where('status', 'scheduled')
//            ->whereRaw('DATE_ADD(start, INTERVAL duration MINUTE) < ?', [$now])
//            ->get();
//
//        foreach ($cancelledInstances as $instance) {
//            $instance->update(['status' => 'cancelled']);
//            event(new LessonInstanceStatusUpdatedEvent($instance));
//        }
//
//        // Update 'in_progress' lessons to 'completed'
//        $completedInstances = LessonInstance::where('status', 'in_progress')
//            ->whereRaw('DATE_ADD(start, INTERVAL duration MINUTE) < ?', [$now])
//            ->get();
//
//        foreach ($completedInstances as $instance) {
//            $instance->update(['status' => 'completed']);
//            event(new LessonInstanceStatusUpdatedEvent($instance));
//        }

    }
}
