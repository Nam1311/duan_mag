<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class AdminSettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        return view('admin.caidat', compact('setting'));
    }

    public function header()
    {
        $setting = Setting::first();
        return view('admin.header', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'store_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
            'working_hours' => 'nullable',
            'description' => 'nullable',
            'ship_price' => 'required|numeric|min:0',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $setting = Setting::firstOrFail();
        $data = $request->only(['store_name','email','phone','address','working_hours','description','ship_price']);

        if($request->hasFile('logo')){
            $path = $request->file('logo')->store('uploads','public');
            $data['logo'] = $path;
        }

        $setting->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật cài đặt thành công!'
        ]);
    }
}
