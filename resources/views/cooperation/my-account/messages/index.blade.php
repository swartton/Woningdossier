@extends('cooperation.my-account.layouts.app')

@section('my_account_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.my-account.messages.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    @component('cooperation.my-account.layouts.components.chat-messages')
                        @if(isset($coachConversationRequest) && $coachConversationRequest->status == "in behandeling")
                            <li class="left clearfix">

                                <div class="chat-body clearfix">
                                    <div class="header">
                                        <strong class="primary-font">
                                            @lang('woningdossier.cooperation.my-account.messages.index.chat.conversation-requests-consideration.title')
                                        </strong>
                                    </div>
                                    <p>
                                        @lang('woningdossier.cooperation.my-account.messages.index.chat.conversation-requests-consideration.text')
                                    </p>
                                </div>
                            </li>
                        @endif
                        @forelse($mainMessages as $mainMessage)
                            <a href="{{route('cooperation.my-account.messages.edit', ['cooperation' => $cooperation, 'mainMessageId' => $mainMessage->id])}}">
                                <li class="left clearfix">

                                    <div class="chat-body clearfix">
                                        <div class="header">
                                            <strong class="primary-font">
                                                {{$mainMessage->getSender($mainMessage->id)->first_name. ' ' .$mainMessage->getSender($mainMessage->id)->last_name}} - {{ $mainMessage->title }}
                                            </strong>

                                            <small class="pull-right text-muted">
                                                @if($mainMessage->hasUserUnreadMessages() || $mainMessage->isRead() == false)
                                                    <span class="label label-primary">@lang('default.new-message')</span>
                                                @endif
                                                <?php $time = \Carbon\Carbon::parse($mainMessage->created_at) ?>
                                                <span class="glyphicon glyphicon-time"></span> {{ $time->diffForHumans() }}
                                            </small>
                                        </div>
                                        <p>
                                            @if($mainMessage->hasUserUnreadMessages() || $mainMessage->isRead() == false)
                                                <strong>
                                                    {{$mainMessage->message}}
                                                </strong>
                                            @else
                                                {{$mainMessage->message}}
                                            @endif
                                        </p>
                                    </div>
                                </li>
                            </a>

                        @empty
                            @if(isset($coachConversationRequest) != true)
                                <li class="left clearfix">

                                    <div class="chat-body clearfix">
                                        <div class="header">
                                            <strong class="primary-font">
                                                @lang('woningdossier.cooperation.my-account.messages.index.chat.no-messages.title')
                                            </strong>

                                        </div>
                                        <p>
                                            @lang('woningdossier.cooperation.my-account.messages.index.chat.no-messages.text')
                                        </p>
                                    </div>
                                </li>
                            @endif
                        @endforelse
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
@endsection

