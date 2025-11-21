<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwsCredential extends Model
{
    use HasFactory;
     protected $fillable = [
        'access_key_id',
        'secret_access_key',
        'bucket_name',
        'region',
    ];
}
