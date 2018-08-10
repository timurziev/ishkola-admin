@extends('admin.layouts.admin')

@section('title',__('views.admin.users.edit.title', ['name' => $user->name]) )

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            {{ Form::open(['route'=>['admin.users.update', $user->id],'method' => 'put','class'=>'form-horizontal form-label-left', 'files' => true]) }}

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="image" style="padding-top: 0;">
                        Аватар
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input type="file" name="file">
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name" >
                        {{ __('views.admin.users.edit.name') }}
                        <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="name" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('name')) parsley-error @endif"
                               name="name" value="{{ $user->name }}" required>
                        @if($errors->has('name'))
                            <ul class="parsley-errors-list filled">
                                @foreach($errors->get('name') as $error)
                                        <li class="parsley-required">{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="email">
                        {{ __('views.admin.users.edit.email') }}
                        <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="email" type="email" class="form-control col-md-7 col-xs-12 @if($errors->has('email')) parsley-error @endif"
                               name="email" value="{{ $user->email }}" required>
                        @if($errors->has('email'))
                            <ul class="parsley-errors-list filled">
                                @foreach($errors->get('email') as $error)
                                    <li class="parsley-required">{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="phone" >Телефон</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input placeholder="" id="phone" type="tel" class="form-control col-md-7 col-xs-12"
                               name="phone" value="{{ $user->phone }}">
                    </div>
                </div>

                @if(!$user->hasRole('administrator'))
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="notes" >Примечание</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input id="notes" type="text" class="form-control col-md-7 col-xs-12"
                                   name="notes" value="{{ $user->notes }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="active" >
                            {{ __('views.admin.users.edit.active') }}
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="checkbox">
                                <label>
                                    <input id="active" type="checkbox" class="@if($errors->has('active')) parsley-error @endif"
                                           name="active" @if($user->active) checked="checked" @endif value="1">
                                    @if($errors->has('active'))
                                        <ul class="parsley-errors-list filled">
                                            @foreach($errors->get('active') as $error)
                                                <li class="parsley-required">{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="confirmed" >
                            {{ __('views.admin.users.edit.confirmed') }}
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="checkbox">
                                <label>
                                    <input id="confirmed" type="checkbox" class="@if($errors->has('confirmed')) parsley-error @endif"
                                           name="confirmed" @if($user->confirmed) checked="checked" @endif value="1">
                                    @if($errors->has('confirmed'))
                                        <ul class="parsley-errors-list filled">
                                            @foreach($errors->get('confirmed') as $error)
                                                <li class="parsley-required">{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </label>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="password">
                        {{ __('views.admin.users.edit.password') }}
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="password" type="password" class="form-control col-md-7 col-xs-12 @if($errors->has('password')) parsley-error @endif"
                               name="password">
                        @if($errors->has('password'))
                            <ul class="parsley-errors-list filled">
                                @foreach($errors->get('password') as $error)
                                    <li class="parsley-required">{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="password_confirmation">
                        {{ __('views.admin.users.edit.confirm_password') }}
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="password_confirmation" type="password" class="form-control col-md-7 col-xs-12 @if($errors->has('password_confirmation')) parsley-error @endif"
                               name="password_confirmation">
                        @if($errors->has('password_confirmation'))
                            <ul class="parsley-errors-list filled">
                                @foreach($errors->get('password_confirmation') as $error)
                                    <li class="parsley-required">{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="langs">Языки</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <select id="langs" name="langs[]" class="select2" multiple="multiple" style="width: 100%" autocomplete="off">
                            @foreach($langs as $lang)
                                <option @if($user->langs->find($lang->id)) selected="selected" @endif value="{{ $lang->id }}">
                                    {{ $lang->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="roles">
                        {{ __('views.admin.users.edit.roles') }}
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <select id="roles" name="roles[]" class="select2" multiple="multiple" style="width: 100%" autocomplete="off">
                            @foreach($roles as $role)
                                <option @if($user->roles->find($role->id)) selected="selected" @endif value="{{ $role->id }}">
                                        {{ $role->name == 'administrator' ? 'администратор' : '' }}
                                        {{ $role->name == 'moderator' ? 'модератор' : '' }}
                                        {{ $role->name == 'teacher' ? 'преподаватель' : '' }}
                                        {{ $role->name == 'student' ? 'ученик' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if($user->hasRole('student'))
                    @foreach($user->langs as $lang)
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="discount">Скидка на {{ $lang->name }}</label>

                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input id="discount" type="text" class="form-control col-md-7 col-xs-12"
                                           name="discount[]" value="@foreach($user->discounts as $discount){{ $discount->lang_name == $lang->name ? $discount->amount : '' }}@endforeach">
                                    <input type="hidden" name="discount_lang[]" value="{{ $lang->name }}">
                                </div>
                        </div>
                    @endforeach
                @endif

            <div class="form-group">
                    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                        <a class="btn btn-primary" href="{{ URL::previous() }}"> {{ __('views.admin.users.edit.cancel') }}</a>
                        <button type="submit" class="btn btn-success"> {{ __('views.admin.users.edit.save') }}</button>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/users/edit.css')) }}
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/users/edit.js')) }}
@endsection