<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class reviews extends Model
{
    public $timestamps = true;
    protected $fillable = [
        'user_id',
        'product_id',
        'parent_id',
        'comment',
        'rating', // nếu có
        'status' // added status field for moderation
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Products::class);
    }
    
public function replies()
{
    return $this->hasMany(reviews::class, 'parent_id')->with('user', 'replies');
}



    public function parent()
    {
        return $this->belongsTo(reviews::class, 'parent_id');
    }
        public function children()
    {
        return $this->hasMany(reviews::class, 'parent_id');
    }
     // Replies đệ quy, lấy replies con sâu hơn
    public function nestedReplies()
    {
        return $this->replies()->with('nestedReplies', 'user');
    }
}
