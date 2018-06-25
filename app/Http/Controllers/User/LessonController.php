<?php

namespace App\Http\Controllers\User;

use Auth;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Http\Controllers\Controller;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $email = 'ykcontacts@gmail.com';
        $service_url ="https://room.nohchalla.com/mira/service/v2/persons/byLogin/$email";
        $res = $this->sendrequest($service_url,array(),"GET",0);
        $id=$res['personid'];
        $service_url ="https://room.nohchalla.com/mira/service/v2/myMeasures/".$id."/webinars";
        $lessons = $this->sendrequest($service_url,array(),"GET",0);



        return view('user.lessons.index', ['lessons' => $lessons]);
    }

    public function signit($url,$params)
    {
        $appid='system';//идентификатор приложения
        $secretkey='yU7RFszU';//ключ системы
        $ret_params=$params;
//массив передаваемых параметров
        ksort($ret_params);
//сортировка параметров по названию
        $ret_params['appid']=$appid;
//помещение в конец массива параметра appid
        $signstring="$url?";
//формирование строки для подписи начиная с url
        foreach($ret_params as $key=>$val)
        {
            if
            (($val!="")||(gettype($val)!="string"))
            {
                $signstring.="$key=$val&";
//добавление в строку для подписи очередного параметра
            }
        }
        $signstring.="secretkey=$secretkey";
//дополнение строки для подписи параметром secretkey
        $ret_params['sign']=strtoupper(md5($signstring));
//формирование ключа и добавление его в массив параметров
        return $ret_params;
    }

    public function sendrequest($url,$parameters,$method, $ret_crange)
    {
//дополнение массива параметров значениями appid и sign (используется выше описанная функция signit)
        $curl_data=$this->signit($url,$parameters);
        $ch=curl_init();//инициализация дескриптора запроса
        curl_setopt($ch,CURLOPT_ENCODING,'UTF-8');//заданиекодировкизапроса
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//возвратрезультата
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);//делает возможным переход на страницу ошибки
        curl_setopt($ch,CURLOPT_HEADER,$ret_crange); //делаетвозможнымвозвращение заголовка Content-Range
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,$method);//заданиеметодазапроса
        $query=http_build_query($curl_data);//построение строки параметров
        switch($method)
        {
            case "PUT"://для PUT необходимо передавать длину строки параметров
                curl_setopt($ch,CURLOPT_HTTPHEADER,array("Content-Length: ".strlen($query)));
            case "POST"://параметры PUTи POST передаются в теле запроса
                curl_setopt($ch,CURLOPT_POSTFIELDS,$query);
                break;
            case "GET"://для GET и DELETE параметры указываются в заголовке
            case "DELETE":
                $url.="?$query";
        }
        curl_setopt($ch,CURLOPT_URL,$url);//задание url запроса
        $curl_response=curl_exec($ch);//выполнениезапроса
        $response=json_decode($curl_response,true);//парсингрезультатов
        if(!$response)
            $response=$curl_response;//если результат не json
        $code=curl_getinfo($ch,CURLINFO_HTTP_CODE); //получениекодарезультата
        curl_close($ch);//анализответа
        if($code!=200){
            throw new Exception("НеправильныйHTTPкод: ".$code);
        }
        else
            if
            (
                is_array($response)&&isset($response["errorMessage"]))
            {
                throw new Exception("Возвращенаошибка: ".$response["errorMessage"]);
            }
            else
            {
                return $response;
            }
    }

    /**
     * Display a listing of the resource listed by date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function lessons(Request  $request)
    {
        $date = $request['date'];
        $data = $request['data'];
        $schedules = Schedule::whereHas('lesson.users', function ($q) {
            $q->where('user_id', Auth::user()->id);
        })->orderBy('schedule', 'asc');

        if ($request->ajax()) {
            $schedules = $schedules->get();

            foreach ($schedules as $schedule) {
                foreach ($schedule->lesson->users as $user) {
                    if ($user->userHasRole('student')) {
                        $student = $user->name;
                    }
                }
                $title = $schedule->lesson->lang->name;
                $start = $schedule->schedule->format('Y-m-d H:i');;
                $events[] = ['title' => ' | ' . $student . ' | ' . $title, 'start' => $start];
            }

            return $events;
        }

        if ($request['date'] != null) {
            $schedules = Schedule::where('schedule', 'LIKE', "%$date%")->orderBy('schedule', 'asc');
        }

        if ($data != null) {
            $schedules = $schedules->whereHas('lesson.users', function($q) use ($data) {
                $q->where('name', 'LIKE', "%$data%");
            })->orWhereHas('lesson.lang', function ($q) use ($data) {
                $q->where('name', 'LIKE', "%$data%");
            });
        }

        $schedules = $schedules->paginate(20);

        return view('user.lessons.table', ['schedules' => $schedules]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
