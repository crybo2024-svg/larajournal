<?php

namespace App\Service;

use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PostService
{
    /**
     * 投稿を作成する
     */

    public function store(array $data): Post
    {
        try {
            DB::beginTransaction();

            // ログインユーザーIDをセット
            $data['user_id'] = Auth::id();

            // slug自動生成
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            // タグ情報取り出し
            $tagIds = $data['tag_ids'] ?? [];
            unset($data['tag_ids']);

            // 画像保存
            foreach (['preview_image', 'main_image'] as $key) {
                if (!empty($data[$key])) {
                    $data[$key] = Storage::disk('public')->put('/images', $data[$key]);
                }
            }

            // 投稿作成
            $post = Post::create($data);

            // タグ同期
            if ($tagIds) {
                $post->tags()->sync($tagIds);
            }

            DB::commit();
            return $post;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('PostService::store でエラーが発生', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
            ]);
            throw $e;
        }
    }


    /**
     * 投稿を更新する
     */
    public function update(array $data, Post $post): Post
    {
        try {
            DB::beginTransaction();

            // 投稿の所有者チェック
            if ($post->user_id !== Auth::id()) {
                throw new \Exception('この投稿を編集する権限がありません。');
            }

            // タグ情報取り出し
            $tagIds = $data['tag_ids'] ?? [];
            unset($data['tag_ids']);

            // 画像保存
            foreach (['preview_image', 'main_image'] as $key) {
                if (!empty($data[$key])) {
                    $data[$key] = Storage::disk('public')->put('/images', $data[$key]);
                }
            }

            // 投稿更新
            $post->update($data);

            // タグ同期
            if ($tagIds) {
                $post->tags()->sync($tagIds);
            }

            DB::commit();
            return $post;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('PostService::update でエラーが発生', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
            ]);
            throw $e;
        }
    }
}
