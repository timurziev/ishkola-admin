@extends('admin.layouts.admin')

@section('title', 'Группы')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Группы</h2>
                    <ul class="nav navbar-right panel_toolbox" style="min-width: 0;">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-striped projects">
                        <thead>
                        <tr>
                            <th style="width: 1%">Язык</th>
                            <th style="width: 20%">Имя группы</th>
                            <th>Участники</th>
                            <th style="width: 20%">Действие</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($groups as $group)
                            <tr>
                                <td>{{ $group->lang->name }}</td>
                                <td>
                                    <a>{{ $group->name }}</a>
                                    <br>
                                    <small>{{ $group->created_at }}</small>
                                </td>
                                    <td>
                                        <ul class="list-inline">
                                            @foreach($group->users as $user)
                                                <li>
                                                    <a href="{{ route('admin.users.show', [$user->id]) }}" title="{{ $user->name }}" target="_blank">
                                                        <img src="{{ url('uploads/avatars') }}/{{ $user->avatar->name }}" class="avatar" alt="Avatar">
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                <td>
                                    <a href="{{ route('admin.groups.edit', [$group->id]) }}" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i></a>
                                    <a href="{{ route('admin.groups.destroy', [$group->id]) }}" class="btn btn-danger btn-xs" onclick="return confirm('Удалить группу?');"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pull-right">
                        {{ $groups->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection