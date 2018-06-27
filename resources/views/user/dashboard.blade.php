@extends('user.layouts.user')

@section('content')
    <!-- page content -->
    <div class="row tile_count">
        <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
            <span class="count_top"><i class="fa fa-user"></i> Пользователи</span>
            <div class="count">{{ $counts['users'] }}</div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
            <span class="count_top"><i class="fa fa-briefcase"></i> Учителя</span>
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
        @if(Auth::user()->userHasRole('teacher'))
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Календарь занятий</h2>
                        <ul class="nav navbar-right panel_toolbox" style="min-width: 0;">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div id="user-calendar">
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-4">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Последние занятия</h2>
                        <ul class="nav navbar-right panel_toolbox" style="min-width: 0;">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        @foreach($lessons as $lesson)
                            <article class="media event">
                                <a class="pull-left date">
                                    <p class="month">{{  \Date::parse($lesson['mestartdate'])->format('M') }}</p>
                                    <p class="day">{{ \Carbon\Carbon::parse($lesson['mestartdate'])->format('d') }}</p>
                                </a>
                                <div class="media-body">
                                    <a class="title" href="{{ $lesson['link'] }}" target="_blank">{{ $lesson['mename'] }}</a>
                                    <p>Начало: {{ \Carbon\Carbon::parse($lesson['mestartdate'])->format('H:i:s') }}</p>
                                    <p>Окончание: {{ \Carbon\Carbon::parse($lesson['meenddate'])->format('H:i:s') }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
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
