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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request  $request)
    {
        $data = $request['data'];
        $lessons = Lesson::orderBy('created_at');

        if ($data != null) {
            $lessons = $lessons->whereHas('users', function($q) use ($data) {
                $q->where('name', 'LIKE', "%$data%");
            })->orWhereHas('lang', function ($q) use ($data) {
                $q->where('name', 'LIKE', "%$data%");
            });
        }

        $lessons = $lessons->paginate(20);

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
        $teacherId = $request['teacher'];

        $schedules = Schedule::orderBy('schedule', 'desc');

        if ($request->ajax()) {
            $schedules = Schedule::orderBy('schedule', 'asc');

            if ($teacherId !== null) {
                $schedules = $schedules->whereHas('lesson.users', function ($q) use ($teacherId) {
                    $q->where('user_id', $teacherId);
                });
            }

            $schedules = $schedules->get();

            foreach ($schedules as $schedule) {
                foreach ($schedule->lesson->users as $user) {
                    if ($user->hasRole('student')) {
                        $student = $user->name;
                    }
                }
                $title = $schedule->lesson->lang->name;
                $start = $schedule->schedule->format('Y-m-d H:i');;
                $events[] = ['title' => ' | ' . $student . ' | ' . $title, 'start' => $start, 'url' => 'admin/lessons/'.$schedule->lesson->id.'/edit'];
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
            })->orWhereHas('users', function ($q) use ($id) {
                    $q->where('user_id', $id);
            })->with(['payments' => function ($q) use ($id) {
                $q->where('user_id', $id);
            }]);
        }

        $schedules = $schedules->paginate(20);

        return view('admin.lessons.table', ['schedules' => $schedules, 'user_id' => $id]);
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
        $datetimes = $addedDays = $schedules = $DBSchedules = $interGetDays = $interAddedDays = [];
        $quantity = $request['quantity'] ?? 40;

        if (!$request['group_id']) {
            $user_id = User::userIdByRole($request['users'], 'student');
        }
        $teacher_id = User::userIdByRole($request['users'], 'teacher');

        foreach ($request['datetimes'] as $times) {
            $date[] = Carbon::createFromFormat('d.m.Y H:i', $times, 'Europe/Moscow');

            $time = Carbon::createFromFormat('d.m.Y H:i', $times, 'Europe/Moscow');
            $datetimes[] = $time->format('Y-m-d H:i');

            $interGetDays[] = Carbon::createFromFormat('d.m.Y H:i', $times, 'Europe/Moscow')->format('Y-m-d H');
        }

        while (count(array_merge($datetimes, $addedDays)) < $quantity) {
            foreach ($date as $day) {
                $addedDays[] = $day->addDays(7)->format('Y-m-d H:i');

                $interAddedDays[] = $day->format('Y-m-d H');
            }
        }

        $teacherSchedules = Schedule::whereHas('lesson.users', function($q) use ($teacher_id) {
            $q->where('user_id', $teacher_id);
        })->pluck('schedule');

        foreach ($teacherSchedules as $schedule) {
            $DBSchedules[] = Carbon::createFromFormat('Y-m-d H:i:s', $schedule, 'Europe/Moscow')->format('Y-m-d H');
        }

        $intersect = array_intersect($DBSchedules, array_merge($interGetDays, $interAddedDays));

        if ($intersect) {
            return redirect()->back()->with(['message' => 'Обнаружены повторяющиеся даты', 'intersect' => $intersect]);
        }

        $lesson = Lesson::create($request->all());
        $lesson->users()->attach($request['users']);

        foreach (array_merge($datetimes, $addedDays) as $finalDate) {
            $fields = ['lesson_id' => $lesson->id, 'schedule' => $finalDate . ':00'];
            $schedules[] = Schedule::create($fields);
        }

        foreach ($schedules as $schedule) {
            if ($schedule->lesson->group_id) {
                foreach ($schedule->lesson->group->users as $user) {
                    $schedule->users()->attach($user->id);
                    Payment::create(['user_id' => $user->id, 'schedule_id' => $schedule->id]);
                }
            } else {
                Payment::create(['user_id' => $user_id, 'schedule_id' => $schedule->id]);
            }
        }

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
        $schedules = [];

//        $lesson->update($request->all());

        if ($request['edit-datetimes'] != null) {
//            $schedules = Schedule::where('lesson_id', $id);
//            $schedules->delete();

            foreach ($request['edit-datetimes'] as $date) {
                $newdate = Carbon::createFromFormat('d.m.Y H:i', $date, 'Europe/Moscow')->format('Y-m-d H:i');

                $fields = ['lesson_id' => $id, 'schedule' => $newdate . ':00'];

                $schedules[] = Schedule::create($fields);
            }

            foreach ($schedules as $schedule) {
                if ($schedule->lesson->group_id) {
                    foreach ($schedule->lesson->group->users as $user) {
                        Payment::create(['user_id' => $user->id, 'schedule_id' => $schedule->id]);
                    }
                } else {
                    foreach ($schedule->lesson->users as $user) {
                        if ($user->hasRole('student')) {
                            $user_id = $user->id;
                        }
                    }
                    Payment::create(['user_id' => $user_id, 'schedule_id' => $schedule->id]);
                }
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
        $lesson = $lesson->createLessonAPI();

        $message = $lesson ? 'Занятия успешно запланированы' : 'Нет занятий для планирования';

        return redirect()->back()->with('message', $message);
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

        User::sendPaymentInfo($request['user']);

        if ($request->has('comment')) {
            foreach ($request['comment'] as $key => $comment)  {
                $schedule = Schedule::whereId($request['schedule_id'][$key])->first();
                $schedule->update(['comment' => $comment]);
            }
        }

        return redirect()->back();
    }

    public function destroySchedule($id, $user_id)
    {
        $deleting = Schedule::whereId($id)->whereHas('payments', function ($q) use ($user_id) {
            $q->where('user_id', $user_id);
        })->with(['payments' => function ($q) use ($user_id) {
            $q->where('user_id', $user_id);
        }])->first();

        $lesson = new Lesson();
        $url = $lesson->miraURL('measures', null, $deleting->meid);
        $lesson->sendRequest($url, [], "DELETE");

        $next = Schedule::whereHas('payments', function ($q) use ($user_id) {
            $q->where('paid', 0)->where('user_id', $user_id);
        })->whereHas('lesson.users', function($q) use ($user_id) {
            $q->where('user_id', $user_id);
        })->orWhereHas('users', function ($q) use ($user_id) {
            $q->where('user_id', $user_id);
        })->whereHas('payments', function ($q) use ($user_id) {
            $q->where('paid', 0)->where('user_id', $user_id);
        })->with(['payments' => function ($q) use ($user_id) {
            $q->where('user_id', $user_id);
        }])->orderBy('schedule', 'asc')->get();

        $next = $next->where('schedule', '>', $deleting->schedule)->first();


        if($deleting->lesson->group_id) {
            $deleting->users()->detach($user_id);
        } else {
            $deleting->delete();
        }

        if ($deleting->payments[0]->paid && isset($next->payments)) {
            $next->payments[0]->update(['paid' => 1]);
        }

        User::sendPaymentInfo($user_id);

        return redirect()->back();
    }
}
