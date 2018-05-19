<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lang;
use App\Models\Group;
use App\Models\Auth\User\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = Group::paginate(20);

        return view('admin.groups.index', ['groups' => $groups]);
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

        return view('admin.groups.create', ['langs' => $langs, 'users' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = $request->only('name', 'lang_id');
        $group = Group::create($fields);
        $group->users()->attach($request['users']);

        return redirect()->route('admin.groups');
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
        $group  = Group::whereId($id)->firstOrFail();
        $langs = Lang::all();
        $users = User::all();

        return view('admin.groups.create', ['group' => $group, 'langs' => $langs, 'users' => $users]);
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
        $group  = Group::whereId($id)->firstOrFail();
        $fields = $request->only('name', 'lang_id');
        $group->save($fields);
        $group->users()->sync($request['users']);

        return redirect()->back()->with('message', 'Настройки сохранены');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $group  = Group::whereId($id)->firstOrFail();
        $group->delete();

        return redirect()->back();
    }
}
