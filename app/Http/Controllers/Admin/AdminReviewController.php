<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reviews;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminReviewController extends Controller
{
    public function index()
    {
        Carbon::setLocale('vi');

        $reviews = Reviews::with(['user', 'product.thumbnail', 'children.user'])
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'user' => $review->user,
                    'comment' => $review->comment,
                    'rating' => $review->rating,
                    'product_id' => $review->product_id,
                    'product' => $review->product,
                    'created_at' => $review->created_at->timezone('Asia/Ho_Chi_Minh')->diffForHumans(),
                    'is_admin' => $review->user->role === 'admin',

                    'replies' => $review->children->map(function ($reply) {
                        return [
                            'id' => $reply->id,
                            'user' => $reply->user,
                            'comment' => $reply->comment,
                            'created_at' => $reply->created_at->timezone('Asia/Ho_Chi_Minh')->diffForHumans(),
                            'is_admin' => $reply->user->role === 'admin',
                            'product_id' => $reply->product_id,
                        ];
                    }),
                ];
            });

        return view('admin.comments', ['reviews' => $reviews]);
    }

    /**
     * Admin reply to a review
     */
    public function replyComments(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'parent_id' => 'required|exists:reviews,id',
            'reply' => 'required|string|max:1000',
        ]);

        $reply = Reviews::create([
            'user_id' => Auth::id(), // mặc định admin id = 10 nếu chưa có login
            'product_id' => $request->product_id,
            'parent_id' => $request->parent_id,
            'comment' => $request->reply,
            'rating' => null,
        ]);

        // Gửi notification cho người viết review gốc
        $parentReview = Reviews::find($request->parent_id);
        if ($parentReview && $parentReview->user_id) {
            DB::table('notifications')->insert([
                'user_id'    => $parentReview->user_id,
                'review_id'  => $parentReview->id,
                'type'       => 'review_reply',
                'title'      => 'Quản trị viên đã phản hồi bình luận',
                'message'    => "Phản hồi: \"{$request->reply}\"",
                'is_read'    => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Phản hồi đã được gửi thành công!');
    }

    /**
     * Delete review or reply
     */
    public function destroy(Request $request)
    {
        $comment = Reviews::find($request->id);

        if (!$comment) {
            return redirect()->back()->with('error', 'Bình luận không tồn tại!');
        }

        // Gửi thông báo cho user trước khi xóa
        if ($comment->user_id) {
            DB::table('notifications')->insert([
                'user_id'    => $comment->user_id,
                'review_id'  => $comment->id,
                'type'       => 'review_deleted',
                'title'      => 'Bình luận đã bị xóa',
                'message'    => 'Bình luận/Phản hồi của bạn đã bị xóa bởi quản trị viên.',
                'is_read'    => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Nếu là bình luận cha -> xóa luôn reply con
        if ($comment->parent_id === null) {
            $children = Reviews::where('parent_id', $comment->id)->get();
            foreach ($children as $child) {
                if ($child->user_id) {
                    DB::table('notifications')->insert([
                        'user_id'    => $child->user_id,
                        'review_id'  => $child->id,
                        'type'       => 'review_deleted',
                        'title'      => 'Phản hồi đã bị xóa',
                        'message'    => 'Phản hồi của bạn đã bị xóa do bình luận gốc bị xóa.',
                        'is_read'    => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            Reviews::where('parent_id', $comment->id)->delete();
            $comment->delete();

            return redirect()->back()->with('success', 'Đã xóa bình luận và tất cả phản hồi liên quan.');
        }

        // Nếu là bình luận con
        $comment->delete();
        return redirect()->back()->with('success', 'Đã xóa phản hồi thành công!');
    }
}
