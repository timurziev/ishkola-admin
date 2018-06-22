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
        $lessons = Lesson::paginate(20);

        return view('admin.lessons.index', ['lessons' => $lessons]);
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
