<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;

class Conversation extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
        'id',
        'participant',
        'read',
        'name',
    ];

    public function user() {
        return $this->belongsTo(User::class,'participant','id');
    }
}
