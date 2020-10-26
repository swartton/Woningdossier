@component('cooperation.mail.components.message')
    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/confirm-account.salutation', [
             'first_name' => $user->first_name,
             'last_name' => $user->last_name
         ])
    @endcomponent
    <?php
        $cooperationHoomdossierHref = View::make('cooperation.mail.parts.ahref', ['href' => route('cooperation.home', ['cooperation' => $userCooperation])]);
        $confirmHref = View::make('cooperation.mail.parts.ahref', [
            'href' => route('cooperation.auth.confirm.store', ['cooperation' => $userCooperation, 'u' => $user->account->email, 't' => $user->account->confirm_token])
        ]);
        $cooperationWebsiteHref = View::make('cooperation.mail.parts.ahref', [
            'href' => is_null($userCooperation->cooperation_email) ? $userCooperation->website_url : "mailto:" . $userCooperation->cooperation_email,
        ]);
    ?>
    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/confirm-account.text', [
            'hoomdossier_link' => $cooperationHoomdossierHref,
            'home_url' => config('app.url'),
            'confirm_url' => $confirmHref,
            'cooperation_link' => $cooperationWebsiteHref
        ])
    @endcomponent
    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/confirm-account.kind_regards', ['app_name' => config('app.name')])
    @endcomponent
@endcomponent