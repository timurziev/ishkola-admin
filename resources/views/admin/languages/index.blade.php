@extends('admin.layouts.admin')

@section('title', 'Языки')

@section('content')
    <div class="row">
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
                    <th scope="row"><i class="fa fa-flag"></i></th>
                    <td>{{ $lang->name }}</td>
                    <td>{{ $lang->basic_price }}</td>
                    <td>{{ $lang->pro_price }}</td>
                    <td>{{ $lang->indiv_price_60 }}</td>
                    <td>{{ $lang->indiv_price_45 }}</td>
                    <td>
                        <a href="#" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i></a>
                        <a href="#" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pull-right">
            {{ $langs->links() }}
        </div>
    </div>
@endsection