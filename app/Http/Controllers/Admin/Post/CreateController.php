<?php

namespace App\Http\Controllers\Admin\Post;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Contracts\View\Factory as ViewFactory;

class CreateController extends BaseController
{
    public function __invoke(ViewFactory $view_factory)
    {
        // Admin は全件、Reader は自分のデータだけ取得
        $categories = auth()->user()->role === 'admin'
            ? Category::all()
            : Category::where('user_id', auth()->id())->get();

        $tags = auth()->user()->role === 'admin'
            ? Tag::all()
            : Tag::where('user_id', auth()->id())->get();

        return $view_factory->make('admin.post.create', [
            'categories' => $categories,
            'tags'       => $tags,
        ]);
    }
}
