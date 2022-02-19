<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;

class ChatLog extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
        'conversationId',
        'from',
        'message',
        'sentTime',
    ];
}
