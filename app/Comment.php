<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\User;

class Comment extends Model
{
    protected $guarded = [];

    public function replies(){
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'name' => 'Guest Author',
            'email' => 'guest@guest.ru',
            'created_at'    => '2019-11-21 21:19:31'
        ]);
    }

    public function scopeParent($query){
        return $query->whereNull('parent_id');
    }
}
