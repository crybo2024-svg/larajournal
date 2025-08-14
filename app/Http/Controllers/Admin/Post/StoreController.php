<?php

namespace App\Http\Controllers\Admin\Post;

use App\Http\Requests\Admin\Post\StoreRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StoreController extends BaseController
{
    public function __invoke(StoreRequest $request)
    {
        Log::info('StoreController invoked', [
            'request_data' => $request->all(),
        ]);

        try {
            $data = $request->validated();
            // ログインユーザーIDを自動セット
            $data['user_id'] = Auth::id();
            
            Log::info('StoreController validated data', [
                'validated_data' => $data,
            ]);

            $this->service->store($data);

            Log::info('StoreController successfully stored post');
        } catch (\Throwable $e) {
            Log::error('StoreController exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Laravel に500を返させるために再スロー
        }

        return redirect()->route('admin.post.index');
    }
}
