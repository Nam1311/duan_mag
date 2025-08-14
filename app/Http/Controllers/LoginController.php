<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }
    public function login(Request $request)
    {
        // Đánh dấu đây là form login
        session(['form_type' => 'login']);

        $request->validate([
            'email' => 'required',
            'password' => 'required|string|min:8',
        ], [
            'email.required' => 'Vui lòng nhập tên đăng nhập hoặt email.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
        ]);
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $request->email)->first();
        if ($user && $user->is_active == 0) {
            return back()->withErrors([
                'email' => 'Tài khoản chưa được kích hoạt. Vui lòng kiểm tra email để kích hoạt.',
            ])->withInput();
        }

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->is_active == 0) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Tài khoản chưa được kích hoạt. Vui lòng kiểm tra email để kích hoạt.',
                ])->withInput();
            }
            if (Auth::user()->is_locked == 1) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Tài khoản của bạn đã bị khóa.',
                ])->withInput();
            }

            switch ($user->role) {
                case 'admin':
                    return redirect('/admin');
                case 'news_manager':
                    return redirect('/admin/quanlytintuc');
                case 'products_manager':
                    return redirect('/admin/products');
                    // sửa vào trang của chăm sóc khách hàng
                case 'customer_service':
                    return redirect('/admin/quanlylienhe');
                default:
                    return redirect()->route('home');
            }

            // Đồng bộ giỏ hàng từ session vào database ngay sau khi đăng nhập
            \Log::info('Starting cart sync after login for user: ' . Auth::id());
            $this->syncCartFromSession();

            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->withInput();
    }


    public function register(Request $request)
    {
        // Đánh dấu đây là form register
        session(['form_type' => 'register']);

        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.max' => 'Họ tên không được vượt quá 50 ký tự.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.unique' => 'Email đã được sử dụng.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'is_active' => 0,
        ]);
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            parameters: ['id' => $user->id, 'hash' => sha1($user->email)]
        );


        try {
            Mail::to(users: $user->email)->send(new WelcomeMail($user, $verificationUrl));
        } catch (\Exception $e) {
            \Log::error('Mail Error: ' . $e->getMessage());
        }


        return redirect()->route('showlogin')->with(
            'status',
            'Đăng ký thành công! Vui lòng kiểm tra email để xác thực tài khoản.'
        );
    }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('showlogin');
    }

    public function ForgotPassword(Request $request)
    {
        // Đánh dấu đây là form forgot password
        session(['form_type' => 'forgot']);

        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email không tồn tại trong hệ thống.'])->withInput();
        }

        // Tạo link đặt lại mật khẩu (dùng route có sẵn hoặc tự tạo)
        $resetUrl = URL::temporarySignedRoute(
            'password.reset', // Đảm bảo bạn có route này
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        try {
            \Mail::to($user->email)->send(new \App\Mail\ForgotPasswordMail($user, $resetUrl));
        } catch (\Exception $e) {
            \Log::error('Mail Error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Không gửi được email. Vui lòng thử lại sau.']);
        }

        return back()->with('status', 'Đã gửi hướng dẫn đặt lại mật khẩu đến email của bạn.');
    }
    public function showResetForm(Request $request, $id, $hash)
    {
        // Kiểm tra link hợp lệ
        if (!$request->hasValidSignature()) {
            abort(403, 'Link không hợp lệ hoặc đã hết hạn.');
        }
        $user = User::findOrFail($id);
        if (sha1($user->email) !== $hash) {
            abort(403, 'Link không hợp lệ.');
        }
        return view('emails.auth.reset-password', ['user' => $user]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = User::findOrFail($request->id);
        $user->password = \Hash::make($request->password);
        $user->save();

        return redirect()->route('showlogin')->with('status', 'Đặt lại mật khẩu thành công! Bạn có thể đăng nhập.');
    }

    /**
     * Đồng bộ giỏ hàng từ session vào database khi user đăng nhập
     */
    private function syncCartFromSession()
    {
        if (!Auth::check()) {
            \Log::info('Cart sync failed: User not authenticated');
            return;
        }

        $sessionCart = session()->get('cart', []);
        \Log::info('Session cart data:', ['cart' => $sessionCart]);

        if (empty($sessionCart)) {
            \Log::info('Cart sync skipped: Session cart is empty');
            return;
        }

        $userId = Auth::id();
        $syncedCount = 0;

        foreach ($sessionCart as $item) {
            \Log::info('Processing cart item:', ['item' => $item]);

            if (empty($item['product_variant_id']) || empty($item['quantity'])) {
                \Log::warning('Skipping invalid cart item:', ['item' => $item]);
                continue;
            }

            // Kiểm tra xem sản phẩm đã có trong giỏ hàng database chưa
            $existingCartItem = \App\Models\Cart::where('user_id', $userId)
                ->where('product_variant_id', $item['product_variant_id'])
                ->first();

            if ($existingCartItem) {
                // Nếu đã có, cộng thêm số lượng
                $oldQuantity = $existingCartItem->quantity;
                $existingCartItem->quantity += $item['quantity'];
                $existingCartItem->save();
                \Log::info('Updated existing cart item:', [
                    'variant_id' => $item['product_variant_id'],
                    'old_quantity' => $oldQuantity,
                    'added_quantity' => $item['quantity'],
                    'new_quantity' => $existingCartItem->quantity
                ]);
            } else {
                // Nếu chưa có, tạo mới
                \App\Models\Cart::create([
                    'user_id' => $userId,
                    'product_variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity'],
                ]);
                \Log::info('Created new cart item:', [
                    'variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity']
                ]);
            }
            $syncedCount++;
        }

        // Xóa giỏ hàng trong session sau khi đồng bộ
        session()->forget('cart');

        \Log::info('Cart sync completed:', [
            'user_id' => $userId,
            'total_items' => count($sessionCart),
            'synced_items' => $syncedCount
        ]);
    }
}
