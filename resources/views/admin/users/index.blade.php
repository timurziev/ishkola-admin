@extends('admin.layouts.admin')

@section('title', __('views.admin.users.index.title'))

@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%">
            <thead>
            <tr>
                <th>@sortablelink('email', __('views.admin.users.index.table_header_0'),['page' => $users->currentPage()])</th>
                <th>@sortablelink('name',  __('views.admin.users.index.table_header_1'),['page' => $users->currentPage()])</th>
                <th>{{ __('views.admin.users.index.table_header_2') }}</th>
                <th>Языки</th>
                @if(Request::is('admin/roles/*'))
                    <th>Ставка или скидка</th>
                @endif
                <th>@sortablelink('active', __('views.admin.users.index.table_header_3'),['page' => $users->currentPage()])</th>
                <th>@sortablelink('confirmed', __('views.admin.users.index.table_header_4'),['page' => $users->currentPage()])</th>
                <th>@sortablelink('created_at', __('views.admin.users.index.table_header_5'),['page' => $users->currentPage()])</th>
                <th>@sortablelink('updated_at', __('views.admin.users.index.table_header_6'),['page' => $users->currentPage()])</th>
                <th>Действие</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->roles->pluck('name')->implode(',') }}</td>
                    <td>{{ $user->langs->pluck('name')->implode(',') }}</td>
                    @if(Request::is('admin/roles/*') && $user->userHasRole('teacher'))
                        <td>
                            @foreach($user->rates as $rate)
                                {{ $rate->lang_name }}:
                                <br>Индивидуально 60 мин - {{ $rate->indiv_60 }} руб
                                <br>Индивидуально 45 мин - {{ $rate->indiv_45 }} руб
                                <br>Мини группа -  {{ $rate->mini_group }} руб
                                <br>Группа больше 2-х - {{ $rate->large_group }} руб
                                <br>Продвинутый курс - {{ $rate->pro_course }} руб
                                <br><br>
                            @endforeach
                        </td>
                    @elseif(Request::is('admin/roles/*') && $user->userHasRole('student'))
                        <td>
                            @foreach($user->discounts as $discount)
                                {{ $discount->lang_name . ' - ' . $discount->amount }}
                            @endforeach
                        </td>
                    @endif
                    <td>
                        @if($user->active)
                            <span class="label label-primary">{{ __('views.admin.users.index.active') }}</span>
                        @else
                            <span class="label label-danger">{{ __('views.admin.users.index.inactive') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($user->confirmed)
                            <span class="label label-success">{{ __('views.admin.users.index.confirmed') }}</span>
                        @else
                            <span class="label label-warning">{{ __('views.admin.users.index.not_confirmed') }}</span>
                        @endif</td>
                    <td>{{ $user->created_at }}</td>
                    <td>{{ $user->updated_at }}</td>
                    <td>
                        <a class="btn btn-xs btn-primary" href="{{ route('admin.users.show', [$user->id]) }}" data-toggle="tooltip" data-placement="top" data-title="{{ __('views.admin.users.index.show') }}">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-xs btn-info" href="{{ route('admin.users.edit', [$user->id]) }}" data-toggle="tooltip" data-placement="top" data-title="{{ __('views.admin.users.index.edit') }}">
                            <i class="fa fa-pencil"></i>
                        </a>
                        @if(!$user->hasRole('administrator'))
                            <button class="btn btn-xs btn-danger user_destroy"
                                    data-url="{{ route('admin.users.destroy', [$user->id]) }}" data-toggle="tooltip" data-placement="top" data-title="{{ __('views.admin.users.index.delete') }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pull-right">
            {{ $users->links() }}
        </div>
    </div>
@endsection