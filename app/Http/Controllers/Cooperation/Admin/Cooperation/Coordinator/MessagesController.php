<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Http\Requests\Cooperation\Admin\Coach\MessagesRequest;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Services\InboxService;
use App\Services\MessageService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MessagesController extends Controller
{
    public function index()
    {
        $mainMessages = PrivateMessage::myCreatedMessages()->get();

        return view('cooperation.admin.cooperation.coordinator.messages.index', compact('mainMessages'));
    }

    public function edit(Cooperation $cooperation, $mainMessageId)
    {

        $privateMessages = PrivateMessage::conversation($mainMessageId)->get();

        InboxService::setRead($mainMessageId);

        return view('cooperation.admin.cooperation.coordinator.messages.edit', compact('privateMessages'));
    }

    public function store(Cooperation $cooperation, MessagesRequest $request)
    {
        MessageService::create($request);

        return redirect()->back();
    }
}