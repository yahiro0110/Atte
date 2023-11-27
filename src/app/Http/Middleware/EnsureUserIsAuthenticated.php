<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * このミドルウェアは、現在のユーザーが認証されているかどうかをチェックする。
     * ユーザーが認証されていない場合は、指定された警告ルートにリダイレクトする。
     *
     * @param  \Illuminate\Http\Request  $request  現在のリクエストインスタンス
     * @param  \Closure  $next  次のミドルウェアへのコールバック
     * @return \Illuminate\Http\Response|mixed ユーザーが認証されている場合、次のミドルウェアにリクエストを渡す。
     *                                         認証されていない場合は、特定のルートにリダイレクトする。
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('warning');
        }
        return $next($request);
    }
}
