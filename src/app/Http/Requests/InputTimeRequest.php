<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InputTimeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ];
    }

    public function messages()
    {
        return [
            'start_time.required' => '出勤時間を入力してください',
            'start_time.date_format' => '出勤時間は時:分の形式で入力してください',
            'end_time.required' => '退勤時間を入力してください',
            'end_time.date_format' => '退勤時間は時:分の形式で入力してください',
            'end_time.after' => '退勤時間は出勤時間より後でなければなりません',
        ];
    }

    /**
     * バリデータにカスタムバリデーションを追加する。
     *
     * @param \Illuminate\Validation\Validator $validator バリデータのインスタンス
     */
    public function withValidator($validator)
    {
        // バリデーションルールがすべてチェックされた後に実行されるクロージャを定義
        $validator->after(function ($validator) {
            // 休憩時間のバリデーションを実行
            $this->validateBreakTimes($validator);
        });
    }

    /**
     * 休憩時間の開始時刻と終了時刻のバリデーションをおこなう。
     * 休憩時間の開始時刻が終了時刻よりも後であれば、エラーメッセージを追加する。
     *
     * @param \Illuminate\Validation\Validator $validator バリデータのインスタンス
     */
    private function validateBreakTimes($validator)
    {
        // 休憩時間の開始時間を取得
        $startTimes = $this->input('breaktime_start_time');
        // 休憩時間の終了時間を取得
        $endTimes = $this->input('breaktime_end_time');

        // 開始時間または終了時間が配列でない場合は処理を中断
        if (!is_array($startTimes) || !is_array($endTimes)) {
            return;
        }

        // 開始時間と終了時間をループでチェック
        foreach ($startTimes as $index => $startTime) {
            // 開始時間または終了時間がセットされていない場合はスキップ
            if (!isset($startTime, $endTimes[$index])) {
                continue;
            }

            // 開始時間が終了時間よりも後または同じ時刻であればエラーを追加
            if ($startTime >= $endTimes[$index]) {
                $validator->errors()->add("breaktime_end_time.{$index}", '終了時間は開始時間より後でなければなりません');
            }
        }
    }
}
