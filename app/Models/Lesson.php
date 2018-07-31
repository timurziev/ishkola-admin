<?php

namespace App\Models;

use Auth;
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

    protected $fillable = ['lang_id', 'group_id', 'format', 'duration', 'price', 'quantity'];

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

    public static function paginateArray($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    /**
     * Sign into Mirafox API.
     *
     * @param  int  $url
     * @param  int  $parameters
     * @return \Illuminate\Http\Response
     */
    public function signin($url, $parameters)
    {
        $appid = 'system';
        $secretkey = 'yU7RFszU';
        $ret_params = $parameters;
        ksort($ret_params);
        $ret_params['appid'] = $appid;
        $signstring = "$url?";
        foreach($ret_params as $key => $val)
        {
            if
            (($val != "")||(gettype($val) != "string"))
            {
                $signstring .= "$key=$val&";
            }
        }
        $signstring .= "secretkey=$secretkey";
        $ret_params['sign'] = strtoupper(md5($signstring));

        return $ret_params;
    }

    /**
     * Send request and return array of items from Mirafox API.
     *
     * @param  int  $url
     * @param  int  $parameters
     * @param  int  $method
     * @return \Illuminate\Http\Response
     */
    public function sendRequest($url, $parameters, $method)
    {
        $curl_data = $this->signin($url, $parameters);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $query = http_build_query($curl_data);

        if ($method == "POST") {
            curl_setopt($ch,CURLOPT_POSTFIELDS,$query);
        } elseif ($method == "GET") {
            $url .= "?$query";
        } else {
            curl_setopt($ch,CURLOPT_POSTFIELDS,$query);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        $curl_response = curl_exec($ch);
        $response = json_decode($curl_response, true);

        return $response;
    }

    public function getSessionId()
    {
        $parameters = ['login' => 'guest', 'password' => 'QyYp86Bx'];

        $url = "https://room.nohchalla.com/mira/service/auth/login";

        return $session = $this->sendRequest($url, $parameters, "POST");
    }

    public function lessonsTemplate()
    {
        $email = Auth::user()->email;
        $service_url ="https://room.nohchalla.com/mira/service/v2/persons/byLogin/$email";
        $res = $this->sendRequest($service_url, [], "GET");
        $id = $res['personid'];

        $service_url ="https://room.nohchalla.com/mira/service/v2/myMeasures/$id/webinars";

        return $lessons = $this->sendRequest($service_url, [], "GET");
    }

    public function cachedLessons($email)
    {
        $minutes = Carbon::now()->addMinutes(60);

        $lessons = Cache::remember('lessons-' . $email, $minutes, function () {
            $lesson = new Lesson;

            return $lessons = $lesson->lessonsTemplate();
        });

        return $lessons;
    }

    public function records($id)
    {
        $url ="https://room.nohchalla.com/mira/service/v2/measures/$id/webinarRecords";

        return $records = $this->sendRequest($url, [], "GET");
    }

    public function resources($id)
    {
        $minutes = Carbon::now()->addMinutes(30);

        $resources = Cache::remember('resources-' . $id, $minutes, function () use ($id) {
            $url = "https://room.nohchalla.com/mira/service/v2/measures/$id/resources";
            $session = $this->getSessionId();
            $resources = $this->sendRequest($url, [], "GET");

            $resources['session'] = $session;

            return $resources;
        });

        return $resources;
    }

    public function createLessonAPI()
    {
        $date = Carbon::now()->addDay()->format('Y-m-d');

        $schedules = Schedule::where('schedule', 'like', "%$date%")->get();

        if ($schedules != null) {
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

                $service_url ="https://room.nohchalla.com/mira/service/v2/measures";
                $measure = $this->sendRequest($service_url, $parameters, "POST");

                $service_url ="https://room.nohchalla.com/mira/service/v2/measures/".$measure['meid']."/tutors/$teacherID";
                $this->sendRequest($service_url, [], "POST");

                $data = [
                    "lang" => $schedule->lesson->lang->name . " язык",
                    "date" => $schedule->schedule->format('d.m.Y')
                ];

                $payment = Payment::where('schedule_id', $schedule->id)->where('user_id', $user->id)->first();

                $next_schedule = Schedule::where('id', '>', $schedule->id)->first();
                $next_payment = Payment::where('schedule_id', $next_schedule->id)->where('user_id', $user->id)->first();

                $next_data = [
                    "date" => $next_schedule->schedule->format('d.m.Y')
                ];

                if (isset($users)) {
                    foreach ($users as $user) {
                        $service_url = "https://room.nohchalla.com/mira/service/v2/measures/" . $measure['meid'] . "/members/$user->miraID";
                        $this->sendRequest($service_url, [], "POST");

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
                    $service_url = "https://room.nohchalla.com/mira/service/v2/measures/" . $measure['meid'] . "/members/$studentID";
                    $this->sendRequest($service_url, [], "POST");

                    if ($payment->paid == 0) {
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
        }
    }
}
