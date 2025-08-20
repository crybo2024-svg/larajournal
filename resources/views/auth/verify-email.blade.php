@extends('layouts.wrapper-auth')

@section('content')
<div class="card shadow-lg border-0 rounded-4">
    <div class="card-header text-center py-4">
        <h4>{{ __('メール認証が必要です') }}</h4>
    </div>
    <div class="card-body p-4 p-lg-5">
        <p>{{ __('登録ありがとうございます！メールに送信されたリンクをクリックして認証を完了してください。もしメールが届かない場合は再送信できます。') }}</p>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success">
                {{ __('新しい認証リンクが登録時のメールアドレスに送信されました。') }}
            </div>
        @endif

        <div class="d-flex justify-content-between mt-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary">{{ __('認証メールを再送信') }}</button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-link text-decoration-none">{{ __('ログアウト') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
