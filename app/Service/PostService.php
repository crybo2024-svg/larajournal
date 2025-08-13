<?php

namespace App\Service;

use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // 追加
use Illuminate\Support\Str;

/**
 * TODO Abolish.
 *
 * @deprecated
 */
class PostService
{
    public function store($data)
    {
        try {
            DB::beginTransaction();

            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            if (isset($data['tag_ids'])) {
                $tagIds = $data['tag_ids'];
                unset($data['tag_ids']);
            }
            if (isset($data['preview_image'])) {
                $data['preview_image'] = Storage::disk('public')->put('/images', $data['preview_image']);
            }
            if (isset($data['main_image'])) {
                $data['main_image'] = Storage::disk('public')->put('/images', $data['main_image']);
            }

            $post = Post::firstOrCreate($data);

            if (isset($tagIds)) {
                $post->tags()->attach($tagIds);
            }
            if (isset($data['category_id'])) {
                $categoryId = $data['category_id'];
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            // 🔽 ここを追加：ログ出力
            Log::error('PostService::store でエラーが発生', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
            ]);

            // 🔽 ここを修正：abort(500) から例外スローに変更
            throw $e;
        }
    }

    public function update($data, $post)
    {
        try {
            DB::beginTransaction();
            if (isset($data['tag_ids'])) {
                $tagIds = $data['tag_ids'];
                unset($data['tag_ids']);
            }
            // カテゴリ処理
            $post->update($data);
            
            if (isset($data['preview_image'])) {
                $data['preview_image'] = Storage::disk('public')->put('/images', $data['preview_image']);
            }
            if (isset($data['main_image'])) {
                $data['main_image'] = Storage::disk('public')->put('/images', $data['main_image']);
            }

            $post->update($data);

            if (isset($tagIds)) {
                $post->tags()->sync($tagIds);
            }
            // カテゴリの同期（中間テーブルに反映）
            if (isset($categoryId)) {
                $data['category_id'] = $categoryId;
            }
            
            DB::commit();
        } catch (Exception) {
            DB::rollBack();
            abort(500);
        }

        return $post;
    }
}
