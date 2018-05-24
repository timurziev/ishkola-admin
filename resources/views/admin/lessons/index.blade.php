@extends('admin.layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Занятия</h2>
                    <ul class="nav navbar-right panel_toolbox" style="min-width: 0;">
                        <li>
                            <form action="{{ route('admin.lessons.create') }}">
                                <button class="btn btn-default">Добавить новое</button>
                            </form>
                        </li>
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
                                <th>Флаг</th>
                                <th>Язык</th>
                                <th>Базовый курс в группе</th>
                                <th>Продвинутый курс в группе</th>
                                <th>Индивидуально 60 мин</th>
                                <th>Индивидуально 45 мин</th>
                                <th>Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($lessons as $lessons)
                                <tr>
                                    <th scope="row"></th>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <a href="" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i></a>
                                        <a href="" class="btn btn-danger btn-xs" onclick="return confirm('Удалить занятие?');"><i class="fa fa-trash"></i></a>
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