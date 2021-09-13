<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crud extends Model
{
    use HasFactory;
    protected $table = 'users_info';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'email',
        'address',
        'created_at',
    ];
}
