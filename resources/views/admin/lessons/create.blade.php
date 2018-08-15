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
                        {{ Form::open(['route' => ['admin.lessons.update', $lesson->id], 'method' => 'put', 'class' => 'form-horizontal form-label-left']) }}
                    @else
                        {{ Form::open(['route' => 'admin.lessons.store', 'method' => 'put', 'class' => 'form-horizontal form-label-left']) }}
                    @endif
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Язык<span class="required">*</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <select class="form-control" name="lang_id">
                                @foreach($langs as $lang)
                                    <option @if (Request::is('*/edit') && $lesson->lang_id == $lang->id) selected @endif
                                    value="{{ $lang->id }}">{{ $lang->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="input-options">Учитель и/или ученик</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <select required multiple name="users[]" class="form-control select-user" placeholder="Введите email или имя пользователя..." style="width: 100%">
                                <option value="">Введите email или имя пользователя...</option>
                                @foreach($users as $user)
                                    <option active value="{{ $user->id }}"
                                            @if ( (Request::is('*/edit') && in_array($user->id, $lesson->userList)))
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
                            <select class="form-control" name="group_id">
                                <option {{ !Request::is('*/edit') ? 'selected' : '' }} value="">Не выбрана</option>
                                @foreach($groups as $group)
                                    <option {{ Request::is('*/edit') && $lesson->group_id == $group->id ? 'selected' : '' }}
                                        value="{{ $group->id }}">{{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="lesson_duration">Продолжительность урока <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="lesson_duration" name="duration" @if (Request::is('*/edit')) value="{{ $lesson->duration }}" @endif  class="form-control col-md-7 col-xs-12" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="lesson_format">Формат урока <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="lesson_format" name="format" @if (Request::is('*/edit')) value="{{ $lesson->format }}" @endif  class="form-control col-md-7 col-xs-12" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="price">Стоимость за урок с человека <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="price" name="price" @if (Request::is('*/edit')) value="{{ $lesson->price }}" @endif  class="form-control col-md-7 col-xs-12" required>
                        </div>
                    </div>
                    @if (Request::is('*/create'))
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="quantity">Количество занятий</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="quantity" placeholder="По умолчанию 40..." name="quantity" @if (Request::is('*/edit')) value="{{ $lesson->quantity }}" @endif  class="form-control col-md-7 col-xs-12">
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="reservation-time">Дата и время занятий <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="controls">
                                @if (Request::is('*/edit'))
                                    @foreach($lesson->schedules as $key => $schedule)
                                    <div class="input-prepend input-group datetimes" @if($key != 0) id="parent-input-{{ $key }}" @else id="parent-input" @endif>
                                        <span class="add-on input-group-addon">
                                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                        </span>
                                        <input type="text" name="datetimes[]" id="reserve" class="form-control datetime" value="{{ $schedule->schedule->format('d.m.Y H:i') }}">
                                        {{--@if($key == 0)--}}
                                            {{--<a href="" id="date-time" style="top: 9px; position: absolute; padding-left: 8px;">--}}
                                                {{--<i class="glyphicon glyphicon-plus"></i>--}}
                                            {{--</a>--}}
                                        {{--@endif--}}
                                        <a href="" @if($key == 0) class="hide" @else class="remove" @endif id="remove-date" style="top: 9px; position: absolute; margin-left: 8px;">
                                            <i class="glyphicon glyphicon-remove"></i>
                                        </a>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="input-prepend input-group datetimes" id="parent-input">
                                        <span class="add-on input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="text" name="datetimes[]" id="reserve" class="form-control datetime" value="">
                                        {{--<a href="" id="date-time" style="top: 9px; position: absolute; padding-left: 8px;">--}}
                                            {{--<i class="glyphicon glyphicon-plus"></i>--}}
                                        {{--</a>--}}
                                        <a href="" class="hide" id="remove-date" style="top: 9px; position: absolute; margin-left: 25px;">
                                            <i class="glyphicon glyphicon-remove"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <button class="btn btn-default" id="date-time" style="border: 1px solid #169F85;">Добавить</button>
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            <button type="submit" class="btn btn-success" style="margin-bottom: 0; padding-right: 10px">{{ Request::is('*/edit') ? 'Отправить' : 'Создать' }}</button>
                            @if (session('message'))
                                <span class="red">{{ session('message') }}</span>
                                @foreach(session('intersect') as $date)
                                    <span>{{ $date . ' часов, ' }}</span>
                                @endforeach
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