<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\PublishNews;
use App\Models\NewCategory;
use App\Models\News;
use Illuminate\Auth\Events\Validated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class NewAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = News::query();
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // if($request->filled('dateFillter')){
        //     $query->where('created_at', $request->dateFillter);
        // }





        $news = $query->paginate(5)->withQueryString();
        $categories = NewCategory::withCount([
            'news' => function (Builder $query) {
                $query->where('status', 'Đã xuất bản');
            }
        ])->withSum([
                    'news as total_views' => function (Builder $query) {
                        $query->where('status', 'Đã xuất bản');
                    }
                ], 'views')->get();



        $data = [
            'news' => $news,
            'categories' => $categories,
        ];
        return view('admin.quanlytintuc', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'required|string',
            'category_id' => 'required|integer',
            'author' => 'required|string|max:255',
            'posted_date' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        // Chuyển posted_date về Carbon instance nếu có
        $validated['posted_date'] = $request->posted_date
            ? Carbon::createFromFormat('Y-m-d\TH:i', $request->posted_date)
            : null;

        // Gán status mặc định
        $validated['status'] = 'Chưa xuất bản';

        // Xử lý upload ảnh, gán vào $validated['image'] nếu có
        if ($request->hasFile('image')) {
            $fileName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('img'), $fileName);
            $validated['image'] = $fileName;
        }

        // Tạo bản tin và bắt model trả về
        $news = News::create($validated);

        // Dispatch delayed job hoặc publish ngay
        if ($news->posted_date && $news->posted_date->gt(now())) {
            PublishNews::dispatch($news->id)
                ->delay($news->posted_date);
        } else {
            $news->update(['status' => 'Chưa xuất bản']);
        }

        return redirect()->back()->with('success', 'Tin tức đã được tạo thành công.');
    }


    public function ImageUpload(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|max:2048'
        ]);
        $path = $request->file('upload')->store('uploads', 'public');
        return response()->json(['url' => Storage::url($path)], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $news = News::findOrFail($id);
        $categories = NewCategory::all();
        $data = [
            'news' => $news,
            'categories' => $categories,
        ];
        if (!$news) {
            return redirect()->back()->with('error', 'Tin tức không tồn tại.');
        }
        return view('admin.edit_news', $data);

    }

    /**
     * Update the specified resource in storage.
     */


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'required|string',
            'category_id' => 'required|integer',
            'author' => 'required|string|max:255',
            'posted_date' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $news = News::findOrFail($id);

        // Chuyển posted_date
        $validated['posted_date'] = $request->posted_date
            ? Carbon::createFromFormat('Y-m-d\TH:i', $request->posted_date)
            : null;
        \Log::info("Posted date: " . ($validated['posted_date'] ? $validated['posted_date']->toString() : 'null'));
        \Log::info("Is in future? " . ($validated['posted_date'] && $validated['posted_date']->gt(now()) ? 'yes' : 'no'));
        // Xử lý ảnh
        if ($request->hasFile('image')) {
            $fileName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('img'), $fileName);
            $validated['image'] = $fileName;
        } else {
            $validated['image'] = $news->image;
        }

        // Cập nhật bản tin
        $news->update($validated);

        // Dispatch delayed job hoặc publish ngay
        if ($news->posted_date && $news->posted_date->gt(now())) {
            PublishNews::dispatch($news->id)
                ->delay($news->posted_date);
        } elseif ($news->posted_date && $news->posted_date->lte(now())) {
            $news->update(['status' => 'Đã xuất bản']);
        }

        return redirect()->route('admin.new.index')
            ->with('success', 'Tin tức đã được cập nhật thành công.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $news = News::findOrFail($id);
        if (!$news) {
            return response()->json(['success' => false, 'message' => 'Tin tức không tồn tại.'], 404);
        }
        $news->delete();
        return response()->json(['success' => true, 'message' => 'Tin tức đã được xóa.']);
    }
    public function updateStatus(Request $request, string $id)
    {
        $news = News::findOrFail($id);
        if (!$news) {
            return response()->json(['success' => false, 'message' => 'Tin tức không tồn tại'], 404);
        }
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Đã xuất bản,Chưa xuất bản'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors(),
            ], 422);
        }

        $newStatus = $request->input('status');
        $news->status = $newStatus;


        if ($newStatus === 'Đã xuất bản') {
            $news->posted_date = now();
        }


        $news->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'data' => [
                'id' => $news->id,
                'status' => $news->status,
                'posted_date' => $news->posted_date,
            ],
        ]);
    }

    /**
     * Thêm danh mục tin tức
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:new_categories,name',
            'description' => 'nullable|string|max:500',
        ]);

        NewCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Danh mục đã được thêm thành công'
        ]);
    }

    /**
     * Cập nhật danh mục tin tức
     */
    public function updateCategory(Request $request, $id)
    {
        $category = NewCategory::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:new_categories,name,' . $id,
            'description' => 'nullable|string|max:500',
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Danh mục đã được cập nhật thành công'
        ]);
    }

    /**
     * Xóa danh mục tin tức
     */
    public function destroyCategory($id)
    {
        $category = NewCategory::findOrFail($id);
        
        // Kiểm tra xem danh mục có tin tức không
        if ($category->news()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa danh mục này vì đang có tin tức sử dụng'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Danh mục đã được xóa thành công'
        ]);
    }
}
