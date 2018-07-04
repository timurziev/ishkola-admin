<?php

namespace App\Jobs;

use App\Models\Lesson;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use Cache;

class CacheLessons implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $minutes = Carbon::now()->addMinutes(60);

        $lesson = new Lesson;
        $lessons = $lesson->cachedLessons();

        foreach ($lessons as $key => $item) {
            $resources = Cache::remember('resources-' . $item['meid'], $minutes, function () use ($item, $lesson) {

                $url ="https://room.nohchalla.com/mira/service/v2/measures/" . $item['meid'] . "/resources";
                $session = $lesson->getSessionId();
                $resources = $lesson->sendRequest($url, [], "GET");
                $resources['session'] = $session;

                return $resources;
            });

            if ($key == 2) {
                return $resources;
            }
        }
    }
}
