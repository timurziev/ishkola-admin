@extends('user.layouts.user')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Занятия</h2>
                    <ul class="nav navbar-right panel_toolbox" style="min-width: 0;">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
                               width="100%">
                            <thead>
                                <tr>
                                    <th>Информация</th>
                                    <th>Дата начала</th>
                                    <th>Дата окончания</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lessons as $key => $lesson)
                                    <tr>
                                        <td ><a class="{{ $lesson['meid'] }}" id="id{{ $key }}" href="">{{ $lesson['mename'] }}</a></td>
                                        <td>{{ $lesson['mestartdate'] }}</td>
                                        <td>{{ $lesson['meenddate'] }}</td>
                                    </tr>

                                    <tr class="items-{{ $lesson['meid'] }}" style="display: none">
                                        <td class="resources">
                                            <h4>Материалы:</h4>
                                            <img class="load-res" style="display: none; margin-bottom: 10px;" src="{{ url('/') . '/uploads/images/loader.gif' }}" alt="">
                                        </td>
                                        <td class="records">
                                            <h4>Запись занятия:</h4>
                                            <img class="load-rec" style="display: none; margin-bottom: 10px;" src="{{ url('/') . '/uploads/images/loader.gif' }}" alt="">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pull-right">
                        {{ $lessons->links() }}
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