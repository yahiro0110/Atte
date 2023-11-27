<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureHasRole
{
    /**
     * Handle an incoming request.
     *
     * このミドルウェアは、ユーザーが特定の役割を持っているかどうかをチェックする。
     * 指定された役割を持っていない場合は、指定された警告ルートにリダイレクトする。
     *
     * @param  \Illuminate\Http\Request  $request  現在のリクエストインスタンス
     * @param  \Closure  $next  次のミドルウェアへのコールバック
     * @param  string  $role  検証するためのユーザー役割
     * @return \Illuminate\Http\Response|mixed リクエストが指定された役割を持つユーザーによって行われた場合、次のミドルウェアにリクエストを渡す。
     *                                         そうでない場合は、特定のルートにリダイレクトする。
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::user()->hasRole($role)) {
            return redirect()->route('caution');
        }

        return $next($request);
    }
}
