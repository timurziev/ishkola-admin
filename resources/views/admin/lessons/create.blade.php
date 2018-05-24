@extends('admin.layouts.admin')

@section('content')

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    @if (Request::is('*/edit'))
                        <h2>Редактировать занятие</h2>
                    @else
                        <h2>Создать занятие</h2>
                    @endif
                    <ul class="nav navbar-right panel_toolbox" style="min-width: 0;">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <br>
                    @if (Request::is('*/edit'))
                        {{ Form::open(['route'=> ['admin.groups.update', $group->id],'method' => 'put','class'=>'form-horizontal form-label-left']) }}
                    @else
                        {{ Form::open(['route'=> 'admin.lessons.store','method' => 'put','class'=>'form-horizontal form-label-left']) }}
                    @endif
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Язык<span class="required">*</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <select class="form-control" name="lang_id">
                                @foreach($langs as $lang)
                                    <option @if (Request::is('*/edit') && $group->lang_id == $lang->id) selected @endif
                                    value="{{ $lang->id }}">{{ $lang->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="input-options">Учитель и/или ученик</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <select multiple name="users[]" class="form-control select-user" placeholder="Введите email или имя пользователя..." style="width: 100%">
                                <option value="">Введите email или имя пользователя...</option>
                                @foreach($users as $user)
                                    <option active value="{{ $user->id }}"
                                            @if ( (Request::is('*/edit') && in_array($user->id, $group->userList)))
                                                selected
                                            @endif
                                    >{{ $user->name . ' ' . $user->email }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Группа</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <select class="form-control" name="lang_id">
                                <option selected disabled value="">Не выбрана</option>
                                @foreach($groups as $group)
                                    <option {{ Request::is('*/edit') && $lesson->group_id == $group->id ? 'selected' : '' }}
                                        value="{{ $group->id }}">{{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Продолжительность урока <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="name" @if (Request::is('*/edit')) value="{{ $group->name }}" @endif  class="form-control col-md-7 col-xs-12" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Стоимость за урок с человека <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="name" @if (Request::is('*/edit')) value="{{ $group->name }}" @endif  class="form-control col-md-7 col-xs-12" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Количество занятий <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="name" @if (Request::is('*/edit')) value="{{ $group->name }}" @endif  class="form-control col-md-7 col-xs-12" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="reservation-time">Дата и время занятий <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="controls">
                                <div class="input-prepend input-group datetimes" id="parent-input">
                                    <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                    <input type="text" name="datetimes[]" id="reservation-time" class="form-control datetime" value="01/01/2016 - 01/25/2016">
                                    <a href="" id="date-time" style="top: 9px; float: right; display: inline-block; position: absolute; padding-left: 8px;"><i class="glyphicon glyphicon-plus"></i></a>
                                    <a href="" class="hide" id="remove-date" style="top: 9px; float: right; display: inline-block; position: absolute; margin-left: 25px;"><i class="glyphicon glyphicon-remove"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            <button type="submit" class="btn btn-success" style="margin-bottom: 0; padding-right: 10px">Создать</button>
                            @if (session('message'))
                                {{ session('message') }}
                            @endif
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/users/edit.css')) }}
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/users/edit.js')) }}
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
@endsection