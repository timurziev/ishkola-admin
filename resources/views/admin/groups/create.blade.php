@extends('admin.layouts.admin')

@section('title', 'Группа')

@section('content')

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <ul class="nav navbar-right panel_toolbox" style="min-width: 0;">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <br>
                    @if (Request::is('*/edit'))
                        {{ Form::open(['route'=> ['admin.groups.update', $group->id],'method' => 'put','class'=>'form-horizontal form-label-left']) }}
                    @else
                        {{ Form::open(['route'=> 'admin.groups.store','method' => 'put','class'=>'form-horizontal form-label-left']) }}
                    @endif

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Название группы <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="first-name" name="name" @if (Request::is('*/edit')) value="{{ $group->name }}" @endif required="required" class="form-control col-md-7 col-xs-12">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Язык<span class="required">*</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select class="form-control" name="lang_id">
                                    @foreach($langs as $lang)
                                        <option @if (Request::is('*/edit') && $group->lang_id == $lang->id) selected @endif
                                            value="{{ $lang->id }}">{{ $lang->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="input-options" style="padding-top: 25px;">Пользователи</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                            <span class="tip">(выберите пользователя из списка)</span>
                            <select multiple name="users[]" class="form-control" placeholder="Введите email или имя пользователя..." id="input-options">
                                <option value="">Введите email или имя пользователя...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                            @if ( (Request::is('*/edit') && in_array($user->id, $group->userList)))
                                                selected
                                            @endif
                                    >{{ $user->name . ' ' . $user->email }}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <button type="submit" class="btn btn-success" style="margin-bottom: 0; padding-right: 10px">Создать</button>
                                    @if (session('message'))
                                        {{ session('message') }}
                                    @endif
                            </div>
                        </div>
                    {{ Form::close() }}
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