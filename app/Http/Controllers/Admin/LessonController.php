<?php

namespace App\Http\Controllers\Admin;

use App\Models\Group;
use App\Models\Schedule;
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
        $lesson = Lesson::create($request->all());

        foreach ($request['datetimes'] as $date) {
            $newdate[] = Carbon::createFromFormat('d.m.Y H:i', $date, 'Europe/Moscow');
        }

        $addDays = [];
        $date = [];

        foreach ($newdate as $var) {
            $date[] = $var->format('d.m.Y H:i');
        }

        while (count($addDays) < $request['quantity']) {
            foreach ($newdate as $day) {
                $addDays[] = $day->addDays(7)->format('d.m.Y H:i');
            }
        }

        $mergedDate = array_merge($date, $addDays);

        $number = count($mergedDate) - $request['quantity'];

        array_splice($mergedDate, - $number);

        foreach ($mergedDate as $finalDate) {
            $fields = ['lesson_id' => $lesson->id, 'schedule' => $finalDate];
            Schedule::create($fields);
        }

        $lesson->users()->attach($request['users']);

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
                $newdate = Carbon::createFromFormat('d.m.Y H:i', $date, 'Europe/Moscow')->format('d.m.Y H:i');

                $fields = ['lesson_id' => $id, 'schedule' => $newdate];
//            dd($fields);

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
        //
    }
}
