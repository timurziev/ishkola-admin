<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Models\Auth\User\Avatar;
use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use App\Models\Discount;
use App\Models\Lang;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Support\Facades\Input;
use Image;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $role = null)
    {
        $users = User::whereHas('roles', function($q) use($role) {
            $q->where('role_id', $role);
        });

        if (!$role) {
            $users = User::with('roles');
        }

        if (isset($request['search'])) {
            $search = $request['search'];
            $users = $users->where('name', 'like', "%$search%");
        }

        return view('admin.users.index', ['users' => $users->sortable(['email' => 'asc'])->paginate(20)]);
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(User $user)
    {
        return view('admin.users.show', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', ['user' => $user, 'roles' => Role::get(), 'langs' => Lang::get()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param User $user
     * @return mixed
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'active' => 'sometimes|boolean',
            'confirmed' => 'sometimes|boolean',
        ]);

        $validator->sometimes('email', 'unique:users', function ($input) use ($user) {
            return strtolower($input->email) != strtolower($user->email);
        });

        $validator->sometimes('password', 'min:6|confirmed', function ($input) {
            return $input->password;
        });

        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());

        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->phone = $request->get('phone');
        $user->notes = $request->get('notes');

        if ($request['password'] !== null) {
            $user->password = bcrypt($request->get('password'));
        }

//        if (!Auth::user()->hasRole('administrator')) {
            $user->active = $request->get('active', 0);
            $user->confirmed = $request->get('confirmed', 0);
//        }

        $user->save();

        $this->uploadAvatar();

        //roles
        if ($request->has('roles')) {
            $user->roles()->detach();

            if ($request->get('roles')) {
                $user->roles()->attach($request->get('roles'));
            }
        }

        //langs
        if ($request->has('langs')) {
            $user->langs()->detach();

            if ($request->get('langs')) {
                $user->langs()->attach($request->get('langs'));
            }
        }

        //discount
        if ($request->has('discount')) {
            $discount = Discount::where('user_id', $user->id);
            $discount->delete();

            foreach ($request['discount_lang'] as $key => $lang) {
                if ($request['discount'][$key] != null) {

                    $fields = ['user_id' => $user->id, 'lang_name' => $lang, 'amount' => $request['discount'][$key]];

                    $discount->create($fields);

                }
            }
        }

        return redirect()->intended(route('admin.users'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Upload profile avatar.
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadAvatar()
    {
        if (Input::file('file')) {
            $input = Input::file('file');

            $extension = $input->getClientOriginalExtension();
            $fileName = rand(11111, 99999) . '.' . $extension;
            $destinationPath = public_path('uploads/avatars/' . $fileName);

            Image::make($input)->fit(140, 140)->save($destinationPath);
            Avatar::create(['name' => $fileName, 'user_id' => Auth::user()->id]);
        }
    }
}
