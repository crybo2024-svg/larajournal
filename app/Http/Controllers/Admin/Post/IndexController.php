<?php

namespace App\Http\Controllers\Admin\Post;

use App\Models\Post;
use Illuminate\Contracts\View\Factory as ViewFactory;

class IndexController extends BaseController
{
    public function __invoke(ViewFactory $view_factory)
    {
        $query = Post::with('user', 'category')
                     ->orderBy('created_at', 'desc');

        // Admin以外は自分の投稿だけ取得
        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }

        $posts = $query->get();

        return $view_factory->make('admin.post.index', ['posts' => $posts]);
    }
}
