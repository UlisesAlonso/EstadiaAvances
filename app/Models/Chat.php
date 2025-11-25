<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $table = 'chats';
    protected $primaryKey = 'id_chat';
    public $timestamps = true;

    protected $fillable = [
        'user_one_id',
        'user_two_id',
    ];

    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id', 'id_usuario');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id', 'id_usuario');
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class, 'id_chat', 'id_chat');
    }
}
