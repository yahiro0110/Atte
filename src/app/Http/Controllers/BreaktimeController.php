<?php

namespace App\Http\Controllers;

use App\Models\Breaktime;
use Illuminate\Http\Request;

class BreaktimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Breaktime  $breaktime
     * @return \Illuminate\Http\Response
     */
    public function show(Breaktime $breaktime)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Breaktime  $breaktime
     * @return \Illuminate\Http\Response
     */
    public function edit(Breaktime $breaktime)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Breaktime  $breaktime
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Breaktime $breaktime)
    {
        //
    }

    /**
     * 指定された休憩レコードを削除する。
     *
     * @param  int $breaktimeId  削除する休憩レコードのID
     * @return \Illuminate\Http\JsonResponse 削除成功時のJSONレスポンス
     */
    public function destroy($breaktimeId)
    {
        $breaktime = Breaktime::findOrFail($breaktimeId);
        $breaktime->delete();

        return response()->json('Breaktime deleted successfully');
    }
}
