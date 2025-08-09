<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // Tên bảng
    protected $table = 'settings';

    // Khóa chính
    protected $primaryKey = 'id';

    // Nếu bảng không dùng created_at, updated_at thì đặt false
    public $timestamps = true;

    // Các cột cho phép gán hàng loạt
    protected $fillable = [
        'store_name',
        'description',
        'logo',
        'address',
        'phone',
        'email',
        'working_hours',
        'ship_price',
    ];

    // Nếu muốn format ngày tháng
    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
