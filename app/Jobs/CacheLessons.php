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

    private $email;

    /**
     * Create a new job instance.
     *
     * @param  int  $email
     * @return void
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $lesson = new Lesson;
        $lessons = $lesson->cachedLessons($this->email);
        $minutes = Carbon::now()->addMinutes(60);

        foreach ($lessons as $key => $item) {
            $resources = Cache::remember('resources-' . $item['meid'], $minutes, function () use ($item, $lesson) {

                $url ="https://room.nohchalla.com/mira/service/v2/measures/" . $item['meid'] . "/resources";
                $session = $lesson->getSessionId();
                $resources = $lesson->sendRequest($url, [], "GET");
                $resources['session'] = $session;

                return $resources;
            });

            if ($key == 4) {
                return $resources;
            }
        }
    }
}
