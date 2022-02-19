<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\APIBaseController as APIBaseController;
use App\Models\ChatLog;
use Validator;
use App\Models\Conversation;
use App\Models\DisplayConversation;
use App\Models\User;
use App\Http\Resources\Conversation as ConversationResource;
use App\Http\Resources\DisplayConversation as DisplayConversationResource;

class ConversationController extends APIBaseController
{

    public function findConversationsByUserId($userId)
    {
        $conversationIds = Conversation::select('id')
        ->where('participant',$userId)
        ->distinct()
        ->get();

        // Log::info('******** distinct conversation ids');
        // Log::info(json_encode($conversationIds));

        $conversations = array();
        foreach($conversationIds as $cId) {
            $c = Conversation::where('id','=',$cId['id'])
            ->where('participant','!=',$userId)
            ->first();            
            // Log::info('******** Conversation object');
            // Log::info('****** cId: '.$cId);
            // Log::info(json_encode($c));
            
            $user = $c->user;
            
            // Log::info('****** User');
            // Log::info(json_encode($user));
            $latestChat = DB::table('chat_logs')
            ->where('conversationId','=',$cId['id'])
            ->orderBy('sentTime','desc')
            ->first();

            // Log::info('****** Chat Log');
            // Log::info(json_encode($latestChat));
            $dConversation = new DisplayConversation();
            $dConversation->id = $cId['id'];
            $dConversation->name = $c->name;
            $dConversation->participant = $user->id;
            $dConversation->participantName = $user->name;
            $dConversation->read = $c->read;
            $dConversation->lastMessageTime = ($latestChat != null) ? $latestChat->sentTime : null;
            $dConversation->lastMessage = ($latestChat != null) ? $latestChat->message : "";
            $dConversation->created_at = $c->created_at;
            $dConversation->updated_at = $c->updated_at;

            array_push($conversations, $dConversation);
        }

        return $this->sendResponse(DisplayConversationResource::collection($conversations), 'Conversations fetched');
    }
        
    public function createConversations(Request $request) {
        $input = $request->all();
        $validator = Validator::make($input, [
            'from' => 'required',
            'to' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $toUser = User::find($input['to']);
        if (is_null($toUser)) {
            return $this->sendError('To user does not exist');
        }
        //create conversation record for both parties
        $conversations = array();
        
        try {
            DB::beginTransaction();
            //save conversation for participant 'from'
            $fromConversation = new Conversation();
            $fromConversation->participant = $input['from'];
            $fromConversation->read = false;
            $fromConversation->name = null;
            $fromConversation->save();

            //get the conversationId created
            $conversationId = $fromConversation->id;
            
            //save conversation for participant 'to'
            $toConversation = new Conversation();
            $toConversation->id = $conversationId;
            $toConversation->participant = $input['to'];
            $toConversation->read = false;
            $toConversation->name = null;
            $toConversation->save();
            DB::commit();
            //create display object
            $from = new DisplayConversation();
            $from->id = $fromConversation->id;
            $from->participant = $fromConversation->participant;
            $from->read = false;
            $from->lastMessageTime = null;
            $from->lastMessage = "";
            $from->created_at = $fromConversation->created_at;
            $from->updated_at = $fromConversation->updated_at;
            
            $to = new DisplayConversation();
            $to->id = $toConversation->id;
            $to->participant = $toConversation->participant;
            $to->participantName = $toUser->name;
            $to->read = false;
            $to->lastMessageTime = null;
            $to->lastMessage = "";
            $to->created_at = $toConversation->created_at;
            $to->updated_at = $toConversation->updated_at;
            //add conversations to array and return the array
            array_push($conversations, $from);
            array_push($conversations, $to);
            
        } catch (\PDOException $e) {
            DB::rollback();
            return $this->sendError($e);
        }
        

        return $this->sendResponse(DisplayConversationResource::collection($conversations), 'Conversations created');
        
    }

    public function markAsRead(Request $request, $userId) {
        $input = $request->all();
        $validator = Validator::make($input, [
            'conversationId' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $conversation = Conversation::where([
            'id'=>$input['conversationId'],
            'participant'=>$userId
        ])->get();
        
        if (is_null($conversation)) {
            return $this->sendError('Conversation does not exist');
        }

        $conversation->read = true;
        $conversation->save();
        return $this->sendResponse(new ConversationResource($conversation), 'Conversation updated');
        
    } 

    public function removeEmptyConversation($conversationId) {
        $chats = ChatLog::where('conversationId', $conversationId)->get();
        if (count($chats) === 0) {
            //empty conversation, remove them
            Conversation::find($conversationId)->delete();
            return $this->sendResponse([], 'Conversations deleted');
        }
        return $this->sendResponse([], 'Nothing deleted');
    }
}
