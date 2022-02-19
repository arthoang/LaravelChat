<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use App\Traits\Uuids;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use Uuids;
    public $incrementing = false;

    protected $primaryKey = "id";
    protected $keyType = "string";
}