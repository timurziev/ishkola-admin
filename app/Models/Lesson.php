<?php

namespace App\Models;

use Auth;
use App\Services\MirapolisApi;
use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User\User;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Cache;

class Lesson extends Model
{
    use \Illuminate\Foundation\Bus\DispatchesJobs;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lang_id', 'group_id', 'format', 'duration', 'price', 'quantity'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function getUserListAttribute()
    {
        return $this->users->pluck('id')->toArray();
    }

    /**
     * Return paginated array.
     *
     * @param array $items
     * @param integer $perPage
     * @param integer $page
     * @param array $options
     * @return \Illuminate\Http\Response
     */
    public static function paginateArray($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function getSessionId(MirapolisApi $mirapolis)
    {
        $parameters = ['login' => 'guest', 'password' => 'QyYp86Bx'];

        $url = $mirapolis->miraURL('auth', 'login');

        return $session = $mirapolis->sendRequest($url, $parameters, "POST");
    }

    public function lessonsTemplate(MirapolisApi $mirapolis)
    {
        $email = Auth::user()->email;
        $service_url = $mirapolis->miraURL('persons', 'byLogin', null, $email);
        $res = $mirapolis->sendRequest($service_url, [], "GET");
        $id = $res['personid'];

        $url = $mirapolis->miraURL('myMeasures', 'webinars', $id);

        return $lessons = $mirapolis->sendRequest($url, [], "GET");
    }

    public function cachedLessons($email)
    {
        $minutes = Carbon::now()->addMinutes(60);

        $lessons = Cache::remember('lessons-' . $email, $minutes, function () {

            return $lessons = $this->lessonsTemplate();
        });

        return $lessons;
    }

    public function records($id, $mirapolis)
    {
        $url = $mirapolis->miraURL('measures', 'webinarRecords', $id);

        return $records = $mirapolis->sendRequest($url, [], "GET");
    }

    public function resources($id, $mirapolis)
    {
        $session = Cache::get('session');

        $res = Cache::get('resources-' . $id);

        if ($res == null) {
            $resources = Cache::rememberForever('resources-' . $id, function () use ($id, $mirapolis) {
                $url = $mirapolis->miraURL('measures', 'resources', $id);
                $resources = $mirapolis->sendRequest($url, [], "GET");

                return $resources;
            });
            $resources['session'] = $session;

            return $resources;
        } else {
            $res['session'] = $session;
            return $res;
        }
    }

    public function createLessonAPI(MirapolisApi $mirapolis)
    {
        $date = Carbon::now()->addDay()->format('Y-m-d');

        $schedules = Schedule::where('schedule', 'like', "%$date%")->get();

        if (count($schedules)) {
            foreach ($schedules as $schedule) {
                if ($schedule->lesson->group) {
                    $name = $schedule->lesson->group->name;
                    foreach ($schedule->lesson->group->users as $user) {
                        $users[] = $user;
                    }
                }

                foreach ($schedule->lesson->users as $user) {
                    if ($user->hasRole('student')) {
                        $name = $user->name;
                        $user_id = $user->id;
                        $email = $user->email;
                        $studentID = $user->miraID;
                    }
                    if ($user->hasRole('teacher')) {
                        $teacherID = $user->miraID;
                    }
                }

                $addMin = $schedule->schedule->addMinutes($schedule->lesson->duration);

                $parameters = ["mename" => $schedule->lesson->lang->name . " язык. $name", "metype" => 1, "meeduform" => 1,
                    "mestartdate" => "$schedule->schedule.001",
                    "meenddate" => "$addMin.001"];

                $service_url = $mirapolis->miraURL('measures', null);
                $measure = $mirapolis->sendRequest($service_url, $parameters, "POST");

                $schedule->update(['meid' => $measure['meid']]);

                $service_url = $mirapolis->miraURL('measures', 'tutors', $measure['meid'], $teacherID);
                $mirapolis->sendRequest($service_url, [], "POST");

                $data = [
                    "lang" => $schedule->lesson->lang->name . " язык",
                    "date" => $schedule->schedule->format('d.m.Y')
                ];

                $payment = Payment::where('schedule_id', $schedule->id)->where('user_id', $user_id)->first();
                $next_schedule = Schedule::where('id', '>', $schedule->id)->first();

                if ($next_schedule) {
                    $next_payment = Payment::where('schedule_id', $next_schedule->id)->where('user_id', $user->id)->first();

                    $next_data = [
                        "date" => $next_schedule->schedule->format('d.m.Y')
                    ];
                }

                if (isset($users)) {
                    foreach ($users as $user) {
                        $service_url = $mirapolis->miraURL('measures', 'members', $measure['meid'], $user->miraID);
                        $mirapolis->sendRequest($service_url, [], "POST");

                        if ($payment->paid == 0) {
                            Mail::send('mail.email', $data, function ($message) use ($user) {
                                $message->to($user->email)->subject('Ishkola');
                            });
                        }

                        if (isset($next_payment) && $next_payment->paid == 0) {
                            Mail::send('mail.payment', $next_data, function ($message) use ($user) {
                                $message->to($user->email)->subject('Ishkola');
                            });
                        }
                    }
                } else {
                    $service_url = $mirapolis->miraURL('measures', 'members', $measure['meid'], $studentID);
                    $mirapolis->sendRequest($service_url, [], "POST");

                    if (isset($payment->paid) && $payment->paid == 0) {
                        Mail::send('mail.email', $data, function ($message) use ($email) {
                            $message->to($email)->subject('Ishkola');
                        });
                    }

                    if (isset($next_payment) && $next_payment->paid == 0) {
                        Mail::send('mail.payment', $next_data, function ($message) use ($email) {
                            $message->to($email)->subject('Ishkola');
                        });
                    }
                }
            }
            return true;
        }
    }
}
