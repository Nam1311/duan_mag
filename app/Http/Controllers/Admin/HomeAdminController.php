<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\reviews;
use Illuminate\Support\Facades\Auth;

class HomeAdminController extends Controller
{
    public function show_home()
    {
        $today = Carbon::today('Asia/Ho_Chi_Minh');
        $tomorrow = Carbon::tomorrow('Asia/Ho_Chi_Minh');
        $yesterday = Carbon::yesterday('Asia/Ho_Chi_Minh');
        Carbon::setLocale('vi');

        // Doanh thu hôm nay - tính từ OrderDetail
        $orderIdsToday = Order::whereBetween('created_at', [$today, $tomorrow])
            ->where('status', 'Thành công')
            ->pluck('id');
        $doanhThuHomNay = OrderDetail::whereIn('order_id', $orderIdsToday)
            ->sum('total_final');

        // Doanh thu hôm qua - tính từ OrderDetail
        $orderIdsYesterday = Order::whereBetween('created_at', [$yesterday, $today])
            ->where('status', 'Thành công')
            ->pluck('id');
        $doanhThuHomQua = OrderDetail::whereIn('order_id', $orderIdsYesterday)
            ->sum('total_final');

        // Đơn hàng thành công hôm nay
        $soDonHomNay = Order::whereBetween('created_at', [$today, $tomorrow])
            ->where('status', 'Thành công')
            ->count();

        // Đơn hàng thành công hôm qua
        $soDonHomQua = Order::whereBetween('created_at', [$yesterday, $today])
            ->where('status', 'Thành công')
            ->count();

        // Tổng sản phẩm hôm nay
        $tongSanPhamBanRa = OrderDetail::whereIn('order_id', $orderIdsToday)
            ->sum('quantity');

        // Tổng sản phẩm hôm qua
        $tongSanPhamBanRaHomQua = OrderDetail::whereIn('order_id', $orderIdsYesterday)
            ->sum('quantity');
        // Khách hàng mới hôm nay
        $khachHangMoi = User::whereBetween('created_at', [$today, $tomorrow])->count();

        // Khách hàng mới hôm qua
        $khachHangHomQua = User::whereBetween('created_at', [$yesterday, $today])->count();

        // Tính phần trăm thay đổi
        $phanTramKhachHangMoi = $khachHangHomQua > 0
            ? (($khachHangMoi - $khachHangHomQua) / $khachHangHomQua) * 100
            : null;
        // % thay đổi doanh thu
        $phanTramThayDoiDoanhThu = $doanhThuHomQua > 0
            ? (($doanhThuHomNay - $doanhThuHomQua) / $doanhThuHomQua) * 100
            : null;

        // % thay đổi số đơn
        $phanTramThayDoiSoDon = $soDonHomQua > 0
            ? (($soDonHomNay - $soDonHomQua) / $soDonHomQua) * 100
            : null;

        // % thay đổi số lượng sản phẩm
        $phanTramThayDoiSanPham = $tongSanPhamBanRaHomQua > 0
            ? (($tongSanPhamBanRa - $tongSanPhamBanRaHomQua) / $tongSanPhamBanRaHomQua) * 100
            : null;

        // biểu đồ
        $labels = [];
        $doanhThuTuan = [];
        $sanPhamTuan = [];
        $donHangTuan = [];
        $khachHangTuan = [];

        for ($i = 6; $i >= 0; $i--) {
            $ngay = Carbon::today('Asia/Ho_Chi_Minh')->subDays($i);
            $ngayKeTiep = $ngay->copy()->addDay();

            $labels[] = $ngay->isoFormat('dddd');

            $orderIds = Order::whereBetween('created_at', [$ngay, $ngayKeTiep])
                ->where('status', 'Thành công')->pluck('id');
            $doanhThu = OrderDetail::whereIn('order_id', $orderIds)->sum('total_final');
            $doanhThuTuan[] = $doanhThu;

            $soLuong = OrderDetail::whereIn('order_id', $orderIds)->sum('quantity');
            $sanPhamTuan[] = $soLuong;

            $soDon = Order::whereBetween('created_at', [$ngay, $ngayKeTiep])
                ->where('status', 'Thành công')->count();
            $donHangTuan[] = $soDon;

            $soKH = User::whereBetween('created_at', [$ngay, $ngayKeTiep])->count();
            $khachHangTuan[] = $soKH;
        }
        // top 3 sản phẩm bán chạy
        $startOfWeek = Carbon::now('Asia/Ho_Chi_Minh')->subDays(6)->startOfDay();
        $endOfToday = Carbon::now('Asia/Ho_Chi_Minh')->endOfDay();

        $topSanPham = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_details.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->leftJoin('product_images', function ($join) {
                $join->on('products.id', '=', 'product_images.product_id')
                    ->where('product_images.order', '=', 1); // Chỉ lấy hình chính
            })
            ->where('orders.status', 'Thành công')
            ->whereBetween('orders.created_at', [$startOfWeek, $endOfToday])
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'product_images.path as product_image',
                'products.price',
                'product_categories.name as category_name',
                DB::raw('SUM(order_details.quantity) as total_sold')
            )
            ->groupBy('products.id', 'products.name', 'product_images.path', 'products.price', 'product_categories.name')
            ->orderByDesc('total_sold')
            ->limit(3)
            ->get();

        // đơn hàng gần đây
        $donhangganday = Order::with(['user', 'orderDetails'])->where('created_at', '>=', now()->subDays(7))->orderBy('created_at', 'desc')->take(10)->get();


 $reviews = Reviews::with(['user', 'children.user'])
    ->whereNull('parent_id')
    ->orderBy('created_at', 'desc')
    ->get()
    ->map(function ($review) {
        Carbon::setLocale('vi');
        $createdAt = Carbon::parse($review->created_at)->timezone('Asia/Ho_Chi_Minh');
        $now = Carbon::now('Asia/Ho_Chi_Minh');

        return [
            'id' => $review->id,
            'user' => $review->user,
            'comment' => $review->comment,
            'rating' => $review->rating,
            'product_id' => $review->product_id,
            'created_at' => $createdAt->diffForHumans($now),
            'is_admin' => $review->user->role === 'admin',

            'replies' => $review->children->map(function ($reply) use ($review) {
                return [
                    'id' => $reply->id,
                    'user' => $reply->user,
                    'comment' => $reply->comment,
                    'created_at' => Carbon::parse($reply->created_at)
                        ->timezone('Asia/Ho_Chi_Minh')
                        ->diffForHumans(),
                    'is_admin' => $reply->user->role === 'admin',
                    'product_id' => $review->product_id,
                ];
            }),
        ];
    });

        $data = [
            'doanhThuHomNay' => $doanhThuHomNay,
            'phanTramThayDoiDoanhThu' => $phanTramThayDoiDoanhThu,
            'soDonHomNay' => $soDonHomNay,
            'phanTramThayDoiSoDon' => $phanTramThayDoiSoDon,
            'tongSanPhamBanRa' => $tongSanPhamBanRa,
            'phanTramThayDoiSanPham' => $phanTramThayDoiSanPham,
            'khachHangMoi' => $khachHangMoi,
            'phanTramKhachHangMoi' => $phanTramKhachHangMoi,
            // biểu đồ
            'labels' => $labels,
            'doanhThuTuan' => $doanhThuTuan,
            'sanPhamTuan' => $sanPhamTuan,
            // đéo in ra được vì thằng làm biểu đồ có 2 cái đm nó
            'donHangTuan' => $donHangTuan,
            'khachHangTuan' => $khachHangTuan,
            // top 3 sản phẩm bán chạy
            'topSanPham' => $topSanPham,
            // đơn hàng gần đây
            'donhangganday' => $donhangganday,
            // reviews
            'reviews' => $reviews
        ];

        return view('admin.home', $data);
    }
    public function replyComment(Request $request)
    {
        Reviews::create([
            'user_id' => auth::id(),
            'product_id' => $request->product_id,
            'parent_id' => $request->parent_id,
            'comment' => $request->reply,
            'rating' => null, // phản hồi không cần rating
        ]);

       return redirect()->back()->with('success', 'Phản hồi đã được gửi thành công!');
    }
}
