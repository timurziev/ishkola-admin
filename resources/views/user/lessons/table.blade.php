@extends('user.layouts.user')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Занятия</h2>

                    <ul class="nav navbar-right panel_toolbox" style="min-width: 0;">
                        @if(Request::get('date'))
                            <a href="{{ route('user.lessons_table') }}" class="btn btn-default" style="border: 1px solid #169F85; float: left">Назад</a>
                        @endif
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="col-md-4 col-sm-4 col-xs-12" style="float: right;">
                        <div class='input-prepend input-group'>
                            <input type='text' class="form-control user-calendar" value="{{ Request::get('date') ? \Carbon\Carbon::createFromFormat('Y-m-d', Request::get('date'), 'Europe/Moscow')->format('d.m.Y') : '' }}"/>
                            <span class="add-on input-group-addon">
                                <i class="fa fa-calendar" style="color: #555">
                                </i>
                            </span>
                        </div>
                    </div>
                    {{ Form::open(['route'=> 'user.lessons_table', 'method' => 'get'])  }}
                        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                            <div class="input-group">
                                <input type="text" class="form-control" name="data" placeholder="">
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
                                    <th>Учитель</th>
                                    <th>Дата</th>
                                    <th>Время</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($schedules as $schedule)
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
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
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