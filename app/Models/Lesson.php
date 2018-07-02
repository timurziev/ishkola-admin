<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User\User;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
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
     * @return \Illuminate\Http\Response
     */
    public function sendRequest($url, $parameters)
    {
        $curl_data = $this->signin($url, $parameters);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $query = http_build_query($curl_data);
        $url .= "?$query";
        curl_setopt($ch, CURLOPT_URL, $url);
        $curl_response = curl_exec($ch);
        $response = json_decode($curl_response, true);

        return $response;
    }

    public function lessonsTemplate()
    {
        $lesson = new Lesson;
        $email = Auth::user()->email;
        $service_url ="https://room.nohchalla.com/mira/service/v2/persons/byLogin/$email";
        $res = $lesson->sendRequest($service_url, []);
        $id = $res['personid'];

        $service_url ="https://room.nohchalla.com/mira/service/v2/myMeasures/$id/webinars";

        return $lessons = $lesson->sendRequest($service_url, []);
    }

    public function cachedLessons()
    {
        $minutes = Carbon::now()->addMinutes(60);

        $lessons = Cache::remember('lessons', $minutes, function () {
            $lesson = new Lesson;
            $lessons = $lesson->lessonsTemplate();

//            foreach ($lessons as $item) {
//                $url ="https://room.nohchalla.com/mira/service/v2/measures/" . $item['meid'] . "/webinarRecords";
//
//                $records[] = array_merge($lesson->sendRequest($url, []), $item);
//            }

            return $lessons;
        });

        return $lessons;
    }

    public function records($id)
    {
        $url ="https://room.nohchalla.com/mira/service/v2/measures/$id/webinarRecords";

        return $records = $this->sendRequest($url, []);
    }

    public function resources($id)
    {
        $url2 ="https://room.nohchalla.com/mira/service/v2/measures/$id/resources";

        return $resources = $this->sendRequest($url2, []);
    }

//    public function demoLessons()
//    {
//        $minutes = Carbon::now()->addMinutes(30);
//
//        $demo_lessons = Cache::remember('demo_lessons', $minutes, function () {
//            $lessons = $this->lessonsTemplate();
//
//            return $lessons;
//        });
//
//        return $demo_lessons;
//    }
}
