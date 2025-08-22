<?php

namespace App\Http\Controllers;

use App\Models\NewCategory;
use App\Models\News;
use Illuminate\Http\Request;

class NewController extends Controller
{
    public function show_new()
    {
        $newList = News::where('status', '=', 'Đã xuất bản')->paginate(3);

        $newListView = News::where('status', '=', 'Đã xuất bản')->orderBy('views', 'desc')->take(3)->get();

        $newestNew = News::where('status', '=', 'Đã xuất bản')->orderBy('posted_date', 'desc')->take(3)->get();

        $highlightNews = News::where('status', '=', 'Đã xuất bản')->select('*')
            ->selectRaw('(views / DATEDIFF(NOW(), posted_date + INTERVAL 1 DAY)) as score')
            ->orderByDesc('score')
            ->first();

        $data =  [
            "newList" => $newList,
            "newListView" => $newListView,
            "newestNew" => $newestNew,
            "highlightNews" => $highlightNews,
        ];
        return view('news/news', $data);
    }




    public function new_detail($id)
    {
        $new_detail = News::where('id', $id)->first();
        if ($new_detail) {
            // Tăng lượt xem lên 1
            $new_detail->views += 1;
            $new_detail->save();
        }
        $data =  ["new_detail" => $new_detail];
        return view('news/new_detail', $data);
    }



    public function news_all()
    {
        $news_all = News::where('status', '=', 'Đã xuất bản')->paginate(6);
        $news_category = NewCategory::all();
        $data =  [
            "news_all" => $news_all,
            "news_category" => $news_category
        ];
        return view('news/news_all', $data);
    }
}
