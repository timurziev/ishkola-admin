<?php

namespace App\Http\Controllers\Admin;

use App\Models\Group;
use App\Models\Schedule;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Lang;
use App\Models\Auth\User\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lessons = Lesson::paginate(20);

        return view('admin.lessons.index', ['lessons' => $lessons]);
    }

    /**
     * Display a listing of the resource listed by date.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function lessons(Request  $request, $id = null)
    {
        $date = $request['date'];
        $data = $request['data'];
        $schedules = Schedule::orderBy('schedule', 'asc');

        if ($request->ajax()) {
            $schedules = Schedule::orderBy('schedule', 'asc')->get();

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

        if ($id != null) {
            $schedules = $schedules->whereHas('lesson.users', function($q) use ($id) {
                $q->where('user_id', $id);
            })->orWhereHas('lesson', function ($q) use ($id) {
                $q->whereHas('group.users', function($q) use ($id) {
                    $q->where('user_id', $id);
                });
            })->with(['payments' => function ($q) use ($id) {
                $q->where('user_id', $id);
            }]);
        }

        $schedules = $schedules->paginate(20);

        return view('admin.lessons.table', ['schedules' => $schedules]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $langs = Lang::all();
        $users = User::all();
        $groups = Group::all();

        return view('admin.lessons.create', ['langs' => $langs, 'users' => $users, 'groups' => $groups]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $addDays = [];
        $date = [];
        $time = [];
        $quantity = $request['quantity'] != null ? $request['quantity'] : $request['quantity'] = 40;
        $lesson = Lesson::create($request->all());

        foreach ($request['datetimes'] as $datetime) {
            $newdate[] = Carbon::createFromFormat('d.m.Y H:i', $datetime, 'Europe/Moscow');
        }

        foreach ($newdate as $var) {
            $date[] = $var->format('Y-m-d H:i');
        }


        while (count($addDays) < $quantity) {
            foreach ($newdate as $day) {
                $addDays[] = $day->addDays(7)->format('Y-m-d H:i');
            }
        }

        $mergedDate = array_merge($date, $addDays);

        $number = count($mergedDate) - $request['quantity'];

        array_splice($mergedDate, - $number);

        foreach ($mergedDate as $finalDate) {
            $fields = ['lesson_id' => $lesson->id, 'schedule' => $finalDate . ':00'];
            $schedules[] = Schedule::create($fields);

            $time[] = $finalDate;
        }

        $lesson->users()->attach($request['users']);

        foreach ($request['users'] as $id) {
            $user = User::whereId($id)->first();
            if ($user->hasRole('student')) {
                $user_id = $user->id;
            }
        }

        foreach ($schedules as $schedule) {
            Payment::create(['user_id' => $user_id, 'schedule_id' => $schedule->id]);
        }

//        $lesson->createLessonAPI($lesson, $time, $request['users']);

        return redirect()->route('admin.lessons');
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
        $lesson = Lesson::whereId($id)->firstOrFail();

        return view('admin.lessons.create', ['lesson' => $lesson, 'langs' => $langs = Lang::all(), 'users' => $users = User::all(), 'groups' => $groups = Group::all()]);
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
        $lesson = Lesson::whereId($id)->firstOrFail();

        $lesson->update($request->all());

        if ($request['datetimes'] != null) {
            $schedules = Schedule::where('lesson_id', $id);
            $schedules->delete();

            foreach ($request['datetimes'] as $date) {
                $newdate = Carbon::createFromFormat('d.m.Y H:i', $date, 'Europe/Moscow')->format('Y-m-d H:i');

                $fields = ['lesson_id' => $id, 'schedule' => $newdate . ':00'];

                Schedule::create($fields);
            }
        }

        $lesson->users()->sync($request['users']);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $group  = Lesson::whereId($id)->firstOrFail();
        $group->delete();

        return redirect()->back();
    }

    public function scheduleAPI()
    {
        $lesson = new Lesson();
        $lesson->createLessonAPI();

        return redirect()->back();
    }

    public function payment(Request $request)
    {
        $payments = Payment::where('user_id', $request['user'])->get();

        foreach ($payments as $payment) {
            $condition = null;
            if ($request['schedule'] != null) {
                $condition = in_array($payment->schedule_id, $request['schedule']);
            }
            $condition ? $payment->update(['paid' => 1]) : $payment->update(['paid' => 0]);
        }

        $schedule = Schedule::whereHas('payments', function ($q) {
            $q->where('paid', 1);
        })->first();

        $schedule = $schedule->schedule->format('d.m.Y');

        $user = User::whereId($request['user'])->first();
        $id = $user->miraID;

        $inst = new Lesson();

        $phone = ["personworktel" => "Оплачено до $schedule"];

        $service_url ="https://room.nohchalla.com/mira/service/v2/persons/$id";
        $res = $inst->sendRequest($service_url, $phone, "PUT");

        return redirect()->back();
    }
}
