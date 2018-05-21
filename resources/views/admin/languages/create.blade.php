@extends('admin.layouts.admin')

@section('content')

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Добавить язык</h2>
                    <ul class="nav navbar-right panel_toolbox" style="min-width: 0;">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <br>
                    @if (Request::is('*/edit'))
                        {{ Form::open(['route'=> ['admin.langs.update', $lang->id],'method' => 'put','class'=>'form-horizontal form-label-left', 'files' => true]) }}
                    @else
                        {{ Form::open(['route'=> 'admin.langs.store','method' => 'put','class'=>'form-horizontal form-label-left', 'files' => true]) }}
                    @endif
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="image" style="padding-top: 0;">
                            Флаг
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="file" name="file">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Название языка <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="name" @if (Request::is('*/edit')) value="{{ $lang->name }}" @endif required="required" class="form-control col-md-7 col-xs-12">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="basic">Базовый курс в группе</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="basic" name="basic_price" @if (Request::is('*/edit')) value="{{ $lang->basic_price }}" @endif class="form-control col-md-7 col-xs-12">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pro">Продвинутый курс в группе</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="pro" name="pro_price" @if (Request::is('*/edit')) value="{{ $lang->pro_price }}" @endif class="form-control col-md-7 col-xs-12">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="indiv60">Индивидуально 60 мин</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="indiv60" name="indiv_price_60" @if (Request::is('*/edit')) value="{{ $lang->indiv_price_60 }}" @endif class="form-control col-md-7 col-xs-12">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="indiv45">Индивидуально 45 мин</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="indiv45" name="indiv_price_45" @if (Request::is('*/edit')) value="{{ $lang->indiv_price_45 }}" @endif class="form-control col-md-7 col-xs-12">
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
