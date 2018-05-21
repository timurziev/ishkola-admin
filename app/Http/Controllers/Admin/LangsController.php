<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lang;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Image;

class LangsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $langs = Lang::paginate();

        return view('admin.languages.index', ['langs' => $langs]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.languages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Input::file('file')) {
            $input = Input::file('file');

            $extension = $input->getClientOriginalExtension();
            $fileName = rand(11111, 99999) . '.' . $extension;
            $destinationPath = public_path('uploads/images/' . $fileName);

            Image::make($input)->fit(35, 20)->save($destinationPath);
        }

        $fields = array_merge($request->only([
            'name',
            'basic_price',
            'pro_price',
            'indiv_price_60',
            'indiv_price_45'
        ]), ['image' => $fileName]);

        Lang::create($fields);

        return redirect()->intended(route('admin.langs'));
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
        $lang = Lang::whereId($id)->firstOrFail();

        return view('admin.languages.create', ['lang' => $lang]);
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
        $lang = Lang::whereId($id)->firstOrFail();

        if (Input::file('file')) {
            $input = Input::file('file');

            $extension = $input->getClientOriginalExtension();
            $fileName = rand(11111, 99999) . '.' . $extension;
            $destinationPath = public_path('uploads/images/' . $fileName);

            Image::make($input)->fit(35, 20)->save($destinationPath);
        }

        $fields = array_merge($request->only([
            'name',
            'basic_price',
            'pro_price',
            'indiv_price_60',
            'indiv_price_45'
        ]), ['image' => $fileName]);

        $lang->update($fields);

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
        $group  = Lang::whereId($id)->firstOrFail();
        $group->delete();

        return redirect()->back();
    }
}
