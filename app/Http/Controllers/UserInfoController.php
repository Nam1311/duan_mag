<?php

namespace App\Http\Controllers;

use App\Models\addresses;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserInFoController extends Controller
{
    public function ShowInFo()
    {
        $user = Auth::user();
        $addresses = addresses::where('user_id', $user->id)->get();
        $order = Order::where('user_id', $user->id)->get();

        $data = [
            'user' => $user,
            'addresses' => $addresses,
            'order' => $order,
        ];
        return view('info_user', $data);
    }

    public function huydon($id)
    {
        $order = Order::find($id);
        $order->status = 'Đã hủy';
        $order->save();

            return redirect()->route('infouser', ['tab' => 'orders'])
                     ->with('success', 'Đơn hàng đã được hủy.');
    }

    public function suainfo(Request $request, $id)
    {
        $info = User::find($id);
        if ($request->filled('fullname')) {
            $info->name = $request->input('fullname');
        }
        if ($request->filled('email')) {
            $info->email = $request->input('email');
        }
        if ($request->filled('phone')) {
            $info->phone = $request->input('phone');
        }
        $info->save();

        return redirect()->route('infouser');
    }

    public function mkinfo(Request $request, $id)
    {
        $info = User::find($id);

        // Kiểm tra mật khẩu cũ
        if (!Hash::check($request->old_password, $info->password)) {
            return back()->withErrors(['old_password' => 'Mật khẩu cũ không chính xác.']);
        }

        // Validate mật khẩu mới (nếu có)
        $request->validate([
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'password.min' => 'Mật khẩu phải ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        // Cập nhật mật khẩu nếu người dùng nhập
        if ($request->filled('password')) {
            $info->password = Hash::make($request->password);
        }

        $info->save();

        return redirect()->route('infouser');
    }

    public function themaddress(Request $request)
    {
        $userId = Auth::id();

        $address = new addresses();
        $address->user_id = $userId;
        $address->address = $request->input('address');
        $address->ward = $request->input('ward');
        $address->district = $request->input('district');
        $address->province = $request->input('province');

        $address->is_default = $request->address_type == 1 ? 1 : 0;

        if ($address->is_default == 1) {
            addresses::where('user_id', $userId)->update(['is_default' => 0]);
        }

        $address->save();

        $hasDefault = addresses::where('user_id', $userId)->where('is_default', 1)->exists();
        if (!$hasDefault) {
            $address->is_default = 1;
            $address->save();
        }

        return redirect()->route('infouser', ['tab' => 'address']);
    }

    public function xoaaddress($id)
    {
        $address = addresses::find($id);

        $userId = $address->user_id;
        $isDefault = $address->is_default;

        $address->delete();

        if ($isDefault == 1) {
            $latestAddress = addresses::where('user_id', $userId)
                ->orderByDesc('id')
                ->first();

            if ($latestAddress) {
                $latestAddress->is_default = 1;
                $latestAddress->save();
            }
        }

        return redirect()->route('infouser', ['tab' => 'address']);
    }


    public function suaaddress(Request $request)
    {
        $id = $request->input('address_id');
        $address = addresses::find($id);


        $address->address = $request->input('address');
        $address->ward = $request->input('ward');
        $address->district = $request->input('district');
        $address->province = $request->input('province');
        $isDefault = $request->input('address_type');
        $address->is_default = $isDefault;

        if ($isDefault == 1) {
            addresses::where('user_id', $address->user_id)
                ->where('id', '!=', $address->id)
                ->update(['is_default' => 0]);
        }

        $address->save();

        $hasDefault = addresses::where('user_id', $address->user_id)->where('is_default', 1)->exists();

        if (!$hasDefault) {
            $address->is_default = 1;
            $address->save();
        }

        return redirect()->route('infouser', ['tab' => 'address']);
    }



    // order
    public function Showorder($id)
    {
        $user = Auth::user();
        $addresses = addresses::where('user_id', $user->id)->where('is_default', '=', 1)->get();
        $order = Order::where('user_id', $user->id)->where('id', $id)->get();
        $orderdetail = OrderDetail::with('productVariant.product.images')
            ->whereHas('productVariant.product.images', function ($query) {
                $query->where('order', 1);
            })
            ->where('order_id', $id)
            ->get();


        $data = [
            'user' => $user,
            'addresses' => $addresses,
            'order' => $order,
            'orderdetail' => $orderdetail,
            'tab' => 'order',
        ];
        return view('info_ctdh', $data);
    }
    public function Kiem_tra_login()
    {
        if (Auth::check()) {
            return redirect()->route('infouser');
        } else {
            return redirect()->route('showlogin');
        }
    }
}
