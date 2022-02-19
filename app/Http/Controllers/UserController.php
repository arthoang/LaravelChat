<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\APIBaseController as APIBaseController;
use App\Models\User;
use App\Http\Resources\User as UserResource;


class UserController extends APIBaseController
{

    public function getAllExceptCurrent($userId) {
        $users = User::where('id','<>',$userId)->get();
        return $this->sendResponse(UserResource::collection($users), 'Users fetched');
    }
}
