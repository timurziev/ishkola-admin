<div class="col-md-3 left_col">
    <div class="left_col scroll-view">
        <div class="navbar nav_title" style="border: 0;">
            <a href="{{ route('admin.dashboard') }}" class="site_title">
                <span>{{ config('app.name') }}</span>
            </a>
        </div>

        <div class="clearfix"></div>

        <!-- menu profile quick info -->
        <div class="profile clearfix">
            <div class="profile_pic">
                <img src="{{ url('/uploads/avatars') }}/{{ auth()->user()->avatar ? auth()->user()->avatar->name : 'default.png' }}" alt="..." class="img-circle profile_img">
            </div>
            <div class="profile_info">
                <span>Приветствую, </span>
                <h2>{{ auth()->user()->name }}</h2>
            </div>
        </div>
        <!-- /menu profile quick info -->

        <br/>

        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <h3>{{ __('views.backend.section.navigation.sub_header_0') }}</h3>
                <ul class="nav side-menu">
                    <li>
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fa fa-home" aria-hidden="true"></i>
                            {{ __('views.backend.section.navigation.menu_0_1') }}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="menu_section">
                <h3>{{ __('views.backend.section.navigation.sub_header_1') }}</h3>
                <ul class="nav side-menu">
                    <li class="">
                        <a><i class="fa fa-user"></i> {{ __('views.backend.section.navigation.menu_1_1') }} <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li>
                                <a href="{{ route('admin.users') }}">
                                    <i aria-hidden="true"></i>
                                    Все пользователи
                                    <span class="badge bg-orange">@if ($count['users_unconfirmed'] > 0){{ $count['users_unconfirmed'] }} @endif</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.roles', [$role = 4]) }}">
                                    <i aria-hidden="true"></i>
                                    Учителя
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.roles', [$role = 5]) }}">
                                    <i aria-hidden="true"></i>
                                    Ученики
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('admin.langs') }}"><i class="fa fa-language"></i> Языки</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.groups') }}"><i class="fa fa-group"></i> Группы</a>
                    </li>
                    <li class="">
                        <a href="{{ route('admin.lessons') }}"><i class="fa fa-book"></i> Занятия</a>
                    </li>
                    {{--<li>--}}
                        {{--<a href="{{ route('admin.permissions') }}">--}}
                            {{--<i class="fa fa-key" aria-hidden="true"></i>--}}
                            {{--{{ __('views.backend.section.navigation.menu_1_2') }}--}}
                        {{--</a>--}}
                    {{--</li>--}}
                </ul>
            </div>
            {{--<div class="menu_section">--}}
                {{--<h3>{{ __('views.backend.section.navigation.sub_header_2') }}</h3>--}}

                {{--<ul class="nav side-menu">--}}
                    {{--<li>--}}
                        {{--<a>--}}
                            {{--<i class="fa fa-list"></i>--}}
                            {{--{{ __('views.backend.section.navigation.menu_2_1') }}--}}
                            {{--<span class="fa fa-chevron-down"></span>--}}
                        {{--</a>--}}
                        {{--<ul class="nav child_menu">--}}
                            {{--<li>--}}
                                {{--<a href="{{ route('log-viewer::dashboard') }}">--}}
                                    {{--{{ __('views.backend.section.navigation.menu_2_2') }}--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            {{--<li>--}}
                                {{--<a href="{{ route('log-viewer::logs.list') }}">--}}
                                    {{--{{ __('views.backend.section.navigation.menu_2_3') }}--}}
                                {{--</a>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                    {{--</li>--}}
                {{--</ul>--}}
            {{--</div>--}}
            {{--<div class="menu_section">--}}
                {{--<h3>{{ __('views.backend.section.navigation.sub_header_3') }}</h3>--}}
                {{--<ul class="nav side-menu">--}}
                  {{--<li>--}}
                      {{--<a href="http://netlicensing.io/?utm_source=Laravel_Boilerplate&utm_medium=github&utm_campaign=laravel_boilerplate&utm_content=credits" target="_blank" title="Online Software License Management"><i class="fa fa-lock" aria-hidden="true"></i>NetLicensing</a>--}}
                  {{--</li>--}}
                  {{--<li>--}}
                      {{--<a href="https://photolancer.zone/?utm_source=Laravel_Boilerplate&utm_medium=github&utm_campaign=laravel_boilerplate&utm_content=credits" target="_blank" title="Individual digital content for your next campaign"><i class="fa fa-camera-retro" aria-hidden="true"></i>Photolancer Zone</a>--}}
                  {{--</li>--}}
                {{--</ul>--}}
            {{--</div>--}}
        </div>
        <!-- /sidebar menu -->
    </div>
</div>
