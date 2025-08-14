<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewCategory extends Model
{
    protected $fillable = ['name', 'description'];
    
    public function news()
    {
        return $this->hasMany(News::class, 'category_id');
    }
}
