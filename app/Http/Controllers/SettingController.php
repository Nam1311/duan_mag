<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    function show(){
        $dataSetting = Setting::all();

        $data = [
            'dataSetting' => $dataSetting
        ];

    return view('app', $data);
    }

}
