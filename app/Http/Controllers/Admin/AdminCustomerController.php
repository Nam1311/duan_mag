<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\BulkNotificationMail;

class AdminCustomerController extends Controller
{
    // Hiển thị danh sách khách hàng
    public function index(Request $request)
    {
        $query = User::where('role', 'user');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('id', $request->search);
            });
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_locked', $request->status);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.quanlykhachhang', compact('customers'));
    }

    // Xem chi tiết 1 khách hàng (dùng cho modal)
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    // Thêm mới khách hàng
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string',
            'address'  => 'nullable|string',
        ]);

        $validated['role'] = 'user';
        $validated['is_active'] = 1;
        $validated['is_locked'] = 1;

        $user = User::create($validated);

        return response()->json(['message' => 'Thêm khách hàng thành công!', 'user' => $user]);
    }

    // Cập nhật thông tin khách hàng
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'nullable|string',
            'address'     => 'nullable|string',
            'is_locked'   => 'required|in:0,1',
            'lock_reason' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return response()->json(['message' => 'Cập nhật thành công!', 'user' => $user]);
    }

    // Xoá khách hàng
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Đã xoá khách hàng.']);
    }

    // Đổi trạng thái khoá/mở tài khoản
    public function lockToggle($id)
    {
        $user = User::findOrFail($id);
        $user->is_locked = !$user->is_locked;
        $user->save();

        return response()->json(['message' => 'Đã cập nhật trạng thái tài khoản.']);
    }

    // Gửi mail hàng loạt
    public function sendBulkMail(Request $request)
    {
        $request->validate([
            'ids'     => 'required|array',
            'subject' => 'required|string',
            'content' => 'required|string',
        ]);

        $users = User::whereIn('id', $request->ids)->get();

        foreach ($users as $user) {
           Mail::to($user->email)->send(new BulkNotificationMail($request->subject, $request->content));
        }

        return response()->json(['message' => 'Đã gửi tin nhắn đến người dùng.']);
    }
}
