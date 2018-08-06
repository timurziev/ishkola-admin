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
                                    <th>Язык</th>
                                    <th>Группа/ученик</th>
                                    <th>Преподаватель</th>
                                    <th>Продолжительность урока</th>
                                    <th>Формат урока</th>
                                    <th>Стоимость за урок с человека</th>
                                    <th>Действие</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lessons as $lesson)
                                    <tr>
                                        <th scope="row">{{ $lesson->lang->name }}</th>
                                        @foreach($lesson->users as $user)
                                            @if($user->userHasRole('student'))<td>{{ $user->name }}</td>@endif
                                        @endforeach
                                        @if($lesson->group)<td>{{ $lesson->group->name }}</td>@endif
                                        @foreach($lesson->users as $user)
                                            @if($user->userHasRole('teacher'))<td>{{ $user->name }}</td>@endif
                                        @endforeach
                                        <td>{{ $lesson->duration }}</td>
                                        <td>{{ $lesson->format }}</td>
                                        <td>{{ $lesson->price }}</td>
                                        <td>
                                            <a href="{{ route('admin.lessons.edit', $lesson->id) }}" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i></a>
                                            <a href="{{ route('admin.lessons.destroy', $lesson->id) }}" class="btn btn-danger btn-xs" onclick="return confirm('Удалить занятие?');"><i class="fa fa-trash"></i></a>
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