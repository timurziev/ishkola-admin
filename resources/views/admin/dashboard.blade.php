@extends('admin.layouts.admin')

@section('content')
    <!-- page content -->
    <div class="row tile_count">
        <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
            <span class="count_top"><i class="fa fa-user"></i> Пользователи</span>
            <div class="count">{{ $counts['users'] }}</div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
            <span class="count_top"><i class="fa fa-briefcase"></i> Преподаватели</span>
            <div class="count">{{ $counts['teachers'] }}</div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
            <span class="count_top"><i class="fa fa-mortar-board"></i> Ученики</span>
            <div class="count">{{ $counts['students'] }}</div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
            <span class="count_top"><i class="fa fa-book"></i> Занятия</span>
            <div class="count green">{{ $counts['lessons'] }}</div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
            <span class="count_top"><i class="fa fa-users"></i> Группы</span>
            <div class="count">{{ $counts['groups'] }}</div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
            <span class="count_top"><i class="fa fa-language"></i> Языки</span>
            <div class="count">{{ $counts['langs'] }}</div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Календарь занятий</h2>

                    <ul class="nav navbar-right panel_toolbox" style="min-width: 0;">
                        <li>
                            <form action="{{ route('admin.lessons_table') }}" id="submit" method="POST">
                                <div style="width: 250px;">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="teacher" id="teacher">
                                            @foreach(App\Models\Auth\User\User::whereHas('roles', function ($q) { $q->where('name', 'teacher'); })->get() as $user)
                                                <option class="teacher" value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <button class="btn btn-default">Выбрать</button>
                            </form>
                        </li>
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div id="calendar">
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
@endsection
