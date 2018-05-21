@extends('admin.layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Языки</h2>
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
                            @foreach($langs as $lang)
                                <tr>
                                    <th scope="row"><img src="{{ url('uploads/images') }}/{{ $lang->image }}" alt=""></th>
                                    <td>{{ $lang->name }}</td>
                                    <td>{{ $lang->basic_price }}</td>
                                    <td>{{ $lang->pro_price }}</td>
                                    <td>{{ $lang->indiv_price_60 }}</td>
                                    <td>{{ $lang->indiv_price_45 }}</td>
                                    <td>
                                        <a href="{{ route('admin.langs.edit', [$lang->id]) }}" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i></a>
                                        <a href="{{ route('admin.langs.destroy', [$lang->id]) }}" class="btn btn-danger btn-xs" onclick="return confirm('Удалить язык?');"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pull-right">
                        {{ $langs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection