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

        $lessons = Cache::remember('lessons', $minutes, function () {
            $lesson = new Lesson;
            $lessons = $lesson->tempLessons();

            foreach ($lessons as $item) {
                $url ="https://room.nohchalla.com/mira/service/v2/measures/" . $item['meid'] . "/webinarRecords";

                $records[] = array_merge($lesson->sendRequest($url, []), $item);
            }

            return $lessons = $records;
        });

        return $lessons;
    }
}
