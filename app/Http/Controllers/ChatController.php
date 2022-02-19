<?php

namespace App\Http\Controllers;

use App\Http\Controllers\APIBaseController as APIBaseController;
use Illuminate\Http\Request;
use Validator;
use App\Models\ChatLog;
use App\Http\Resources\ChatLog as ChatLogResource;

class ChatController extends APIBaseController
{
    /**
    * get all the chat logs by a conversation ID
    * @param uuid $conversationId
    */
    public function getChatLogsByConversationId($conversationId) {
        $chatLogs = ChatLog::where('conversationId', $conversationId)
        ->orderBy('sentTime')
        ->get();
        //->orderBy('sentTime','desc')->get();
        return $this->sendResponse(ChatLogResource::collection($chatLogs), 'Chat Logs fetched');
    }

    /**
    * get latest chat logs by a conversation ID
    * @param uuid $conversationId
    * @param request must have lastPolled
    */
    public function getLatestChatLogsByConversationId(Request $request, $conversationId) {
        $input = $request->all();
        $validator = Validator::make($input, [
            'lastPolled' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $chatLogs = ChatLog::where('conversationId', $conversationId)
        ->where('sentTime','>',$input['lastPolled'])
        ->orderBy('sentTime')
        ->get();
        //->orderBy('sentTime','desc')->get();
        return $this->sendResponse(ChatLogResource::collection($chatLogs), 'Chat Logs fetched');
    } 

    /**
    * create a new chat log
    * @param Request
    */
    public function store(Request $request) {
        $input = $request->all();
        $validator = Validator::make($input, [
            'from' => 'required',
            'message' => 'required',
            'conversationId' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $chat = new ChatLog();
        $chat->conversationId = $input['conversationId'];
        $chat->from = $input['from'];
        $chat->message = $input['message'];
        $chat->sentTime = time();
        $chat->save();
        
        return $this->sendResponse(new ChatLogResource($chat), 'Chat Log saved');
    }

}
