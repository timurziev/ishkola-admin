@extends('admin.layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Занятия</h2>

                    <ul class="nav navbar-right panel_toolbox" style="min-width: 0;">
                        @if(Request::get('date'))
                            <a href="{{ route('admin.lessons_table') }}" class="btn btn-default" style="border: 1px solid #169F85; float: left">Назад</a>
                        @endif
                        <li>
                            <form action="{{ route('admin.lessons.api') }}">
                                <button class="btn btn-default schedule" onclick="return confirm('Вы действительно хотите запланировать занятия?');">Запланировать занятия</button>
                            </form>
                            <img class="schedule-loader" style="display: none; margin-bottom: 10px;" src="{{ url('/') . '/uploads/images/loader.gif' }}" alt="">
                        </li>
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="col-md-4 col-sm-4 col-xs-12" style="float: right;">
                        <div class='input-prepend input-group'>
                            <input type='text' class="form-control calendar" value="{{ Request::get('date') ? \Carbon\Carbon::createFromFormat('Y-m-d', Request::get('date'), 'Europe/Moscow')->format('d.m.Y') : '' }}"/>
                            <span class="add-on input-group-addon">
                                <i class="fa fa-calendar" style="color: #555">
                                </i>
                            </span>
                        </div>
                    </div>
                    {{ Form::open(['route'=> 'admin.lessons_table', 'method' => 'get'])  }}
                        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                            <div class="input-group">
                                <input type="text" class="form-control" name="data" value="{{ Request::get('data') }}" placeholder="Введите имя учителя или язык...">
                                <span class="input-group-btn">
                                  <button class="btn btn-default" style="border-left: 1px solid rgba(221, 226, 232, 0.49);">Поиск</button>
                                </span>
                            </div>
                        </div>
                    {{ Form::close() }}
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
                               width="100%">
                            <thead>
                                <tr>
                                    <th>Язык</th>
                                    <th>Группа/ученик</th>
                                    <th>Преподавател</th>
                                    <th>Дата</th>
                                    <th>Время</th>
                                    <th>Комментарий</th>
                                    <th>К оплате</th>
                                    <th>Остаток</th>
                                    <th>Оплата</th>
                                    <th>Действие</th>
                                    <th>Статус</th>
                                </tr>
                            </thead>
                            {{ Form::open(['route'=> 'admin.lessons.payment', 'method' => 'put'])  }}
                                <tbody>
                                    @foreach($schedules as $key => $schedule)
                                        <tr>
                                            <th scope="row">{{ $schedule->lesson->lang->name }}</th>
                                            @foreach($schedule->lesson->users as $user)
                                                @if($user->userHasRole('student'))<td>{{ $user->name }}</td>@endif
                                            @endforeach
                                            @if($schedule->lesson->group)<td>{{ $schedule->lesson->group->name }}</td>@endif
                                            @foreach($schedule->lesson->users as $user)
                                                @if($user->userHasRole('teacher'))<td>{{ $user->name }}</td>@endif
                                            @endforeach
                                            <td>{{ $schedule->schedule->format('d.m.Y') }}</td>
                                            <td>{{ $schedule->schedule->format('H:i') }}</td>
                                            <td class="schedule_comment">
                                                <label for="comment-{{ $key }}" href="" class="comment-pen btn btn-success btn-xs {{ $schedule->comment ? 'hidden' : ''}}">
                                                    <i class="fa fa-pencil-square-o"></i>
                                                </label>
                                                <p class="{{ $schedule->comment ? '' : 'hidden'}}"><input type="text" id="comment-{{ $key }}" name="comment[]" value="{{ $schedule->comment }}" class="form-control col-md-7 col-xs-12"></p>

                                                <input type="hidden" name="schedule_id[]" value="{{ $schedule->id }}">
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>
                                                <a href="{{ route('admin.lessons.edit', $schedule->lesson->id) }}" class="btn btn-info btn-xs">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <a href="{{ route('admin.schedule.destroy', $schedule->id) }}" class="btn btn-danger btn-xs" onclick="return confirm('Удалить занятие?');">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                            <td>
                                                @foreach($schedule->payments as $payment)
                                                    <div class="icheckbox_flat-green {{ $payment->paid ? 'checked' : '' }}" style="position: relative;">
                                                        <input type="checkbox" id="scales-{{ $key }}" name="schedule[]" style="position: absolute; opacity: 0; height: 20px; width: 20px; margin-top: 0;"
                                                               value="{{ $payment->schedule_id }}" {{ $payment->paid ? 'checked' : '' }} />
                                                    </div>
                                                    <input type="hidden" name="user" value="{{ $payment->user_id }}">
                                                    <input type="hidden" name="paid" value="{{ $payment->paid }}">
                                                    <label for="scales-{{ $key }}">{{ $payment->paid ? 'Оплачено' : 'Неоплачено' }}</label>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                        </table>
                        @if (session('message'))
                            <div style="color: green;">{{ session('message') }}</div>
                        @endif

                        <button type="submit" class="btn btn-default" style="border: 1px solid #169F85; float: right">Сохранить</button>
                        {{ Form::close() }}
                    </div>
                    <div class="pull-right">
                        {{ $schedules->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
@endsection