<?php

namespace Chronos\Content\Console;

use App\Console\Kernel as ConsoleKernel;
use Carbon\Carbon;
use Chronos\Content\Models\Content;
use Illuminate\Console\Scheduling\Schedule;

class Kernel extends ConsoleKernel
{
    /**
     * Define the package's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        parent::schedule($schedule);

        // Activate scheduled posts
        $schedule->call(function() {
            $content = Content::where('status_scheduled', '<=', Carbon::now())->get();

            foreach ($content as $item) {
                $item->status = 1;
                $item->status_scheduled = null;
                $item->save();
            }
        })->everyMinute();
    }
}