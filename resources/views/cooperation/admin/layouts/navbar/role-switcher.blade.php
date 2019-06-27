{{--@if(Auth::account()->user()->getRoleNames()->count() == 1)
    <li>
        <a>
            @lang('woningdossier.cooperation.admin.navbar.current-role') {{ Auth::account()->user()->getHumanReadableRoleName(Auth::account()->user()->getRoleNames()->first()) }}
        </a>
    </li>--}}
@if(Auth::account()->user()->getRoleNames()->count() > 1 && \App\Helpers\HoomdossierSession::getRole())
    <li class="dropdown">
        @if(\App\Helpers\HoomdossierSession::hasRole())
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                @lang('woningdossier.cooperation.admin.navbar.current-role') {{ \Spatie\Permission\Models\Role::find(\App\Helpers\HoomdossierSession::getRole())->human_readable_name }}<span class="caret"></span>
            </a>
        @endif

        <ul class="dropdown-menu">
            @foreach(Auth::account()->user()->roles()->orderBy('level', 'DESC')->get() as $role)
                <li>
                    <a href="{{ route('cooperation.admin.switch-role', ['role' => $role->name]) }}">
                        {{ $role->human_readable_name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </li>
@endif