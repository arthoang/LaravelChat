<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;

class DisplayConversation extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
        'id',
        'participantName',
        'read',
        'name',
        'lastMessageTime',
        'lastMessage'
    ];

}
