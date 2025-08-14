<?php

namespace App\Http\Controllers\Admin\Post;

use App\Models\Post;
use Illuminate\Contracts\View\Factory as ViewFactory;

class IndexController extends BaseController
{
    public function __invoke(ViewFactory $view_factory)
    {
        // 投稿者(user) とカテゴリ(category) を同時に取得
        $posts = Post::with('user', 'category')
                    ->orderBy('created_at', 'desc') // 作成日でソート
                    ->get();

        return $view_factory->make('admin.post.index', ['posts' => $posts]);
    }
}
