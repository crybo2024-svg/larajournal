<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // user_id カラムがまだなければ追加
            if (!Schema::hasColumn('posts', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('id');
            }

            // 外部キー制約を追加（まだ存在しなければ）
            // MySQL の場合、制約名を try-catch で回避
            try {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            } catch (\Illuminate\Database\QueryException $e) {
                // すでに存在する場合は無視
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // 外部キー削除
            try {
                $table->dropForeign(['user_id']);
            } catch (\Illuminate\Database\QueryException $e) {
                // 存在しない場合は無視
            }

            // カラム削除
            if (Schema::hasColumn('posts', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};
