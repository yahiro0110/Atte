/**
 * 打刻が読み込まれた後に実行されるイベントリスナー。
 * カウントアップタイマーとフォーム要素のボタンにAjaxでPOST送信を実行するためのイベントハンドラを設定する。
 */

// グローバル変数としてカウントアップタイマーを初期化
let timer = null;
let seconds = 0;

document.addEventListener("DOMContentLoaded", function () {
    // カウントアップタイマーのイベントリスナーを設定
    setupTimerButtonsEventListeners();
    // フォームボタンのイベントリスナーを設定
    setupFormEventListeners();
});

/**
 * カウントアップタイマーを制御するためのボタンにイベントリスナーを設定する。
 */
function setupTimerButtonsEventListeners() {
    document.getElementById("onBreakButton").addEventListener("click", startTimer);
    document.getElementById("offBreakButton").addEventListener("click", stopTimer);
}

/**
 * カウントアップタイマーを開始する。
 * またタイマーが既に動作中であれば、1秒ごとに画面上のタイマー表示を更新する。
 * （カーソルの表示を変更する）
 */
function startTimer() {
    document.body.style.cursor = `url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg'  width='40' height='48' viewport='0 0 100 100' style='fill:black;font-size:24px;'><text y='50%'>🐹</text></svg>")
    16 0, auto`;
    if (timer === null) {
        timer = setInterval(function () {
            updateTimerDisplay();
            seconds++;
        }, 1000);
    }
}

/**
 * カウントアップタイマーを停止する。
 * タイマーが動作中の場合のみ、タイマーを停止し、リセットする。
 */
function stopTimer() {
    document.body.style.cursor = 'auto';
    if (timer !== null) {
        clearInterval(timer);
        timer = null;
    }
}

/**
 * 画面上のタイマー表示を更新する。
 * タイマーの経過時間（秒単位）を時、分、秒に分割し、画面に表示する。
 */
function updateTimerDisplay() {
    let hours = Math.floor(seconds / 3600);
    let minutes = Math.floor((seconds - (hours * 3600)) / 60);
    let secs = seconds % 60;

    // 数値が一桁の場合はゼロを追加して二桁にする
    hours = hours < 10 ? '0' + hours : hours;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    secs = secs < 10 ? '0' + secs : secs;

    // アニメーション効果を付与しタイマー表示を更新
    const timerDisplay = document.getElementById("timerDisplay");
    timerDisplay.classList.remove('fade-in');
    timerDisplay.textContent = `${hours}:${minutes}:${secs}`;
    timerDisplay.classList.add('fade-in');
}

/**
 * 各種のフォーム要素にsubmitイベントリスナーを追加する。
 * 各フォーム要素の送信をAjaxリクエストに変換し、サーバーとの通信を行う。
 */
function setupFormEventListeners() {
    // 各フォーム要素にsubmitイベントリスナーを設定
    addFormSubmitListener("clockIn", updateUIBaseOnWorkStatus);
    addFormSubmitListener("clockOut", updateUIBaseOnWorkStatus);
    addFormSubmitListener("onBreak", updateUIBaseOnWorkStatus);
    addFormSubmitListener("offBreak", updateUIBaseOnWorkStatus);
}

/**
 * 指定された要素にフォーム送信イベントリスナーを設定する。
 * @param {string} elementId - フォーム要素のID
 * @param {Function} callback - イベント発生時に呼び出されるコールバック関数
 */
function addFormSubmitListener(elementId, callback) {
    document.getElementById(elementId).addEventListener("submit", function (e) {
        e.preventDefault(); // デフォルトのフォーム送信を防止
        const jsonData = formDataToJson(new FormData(this)); // フォームデータをJSONに変換
        postData("/punch/" + jsonData["employee_id"], jsonData, callback); // データをサーバーに送信
    });
}

/**
 * FormDataオブジェクトをJSONオブジェクトに変換する。
 * @param {FormData} formData - 変換するFormDataオブジェクト
 * @return {Object} JSONオブジェクト
 */
function formDataToJson(formData) {
    let jsonData = {};
    formData.forEach((value, key) => {
        jsonData[key] = value; // 各キーと値をJSONオブジェクトに追加
    });
    return jsonData;
}

/**
 * 指定されたURLにJSONデータをPOSTリクエストとして送信する。
 * @param {string} url - リクエストを送信するURL
 * @param {Object} jsonData - 送信するJSONデータ
 * @param {Function} callback - レスポンス受信後に呼び出されるコールバック関数
 */
function postData(url, jsonData, callback) {
    fetch(url, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"), // CSRFトークンをヘッダーに含める
            "Accept": "application/json",
            "Content-Type": "application/json",
        },
        body: JSON.stringify(jsonData), // JSONデータを文字列に変換して送信
    })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            callback(data.work_status, data.employee_name); // レスポンスに基づいてコールバック関数を実行
        })
        .catch(error => console.error("Error:", error));
}

/**
 * 勤務状態に基づいてUIを更新する。
 * @param {number} workStatus - 勤務状態を表す数値
 * @param {string} employeeName - 従業員の名前
 */
function updateUIBaseOnWorkStatus(workStatus, employeeName) {
    // アニメーション効果を適用する要素を取得
    const contentMessage = document.querySelector(".content__message");
    const contentImage = document.querySelector(".content__image");
    contentMessage.classList.remove('fade-in'); // アニメーションクラスを一旦削除
    contentImage.classList.remove('fade-in'); // アニメーションクラスを一旦削除

    // 状態に応じたメッセージ、画像ソース、ボタン状態を定義
    const statusConfig = {
        1: {
            message: `${employeeName}さん、今日も一日頑張りましょう！`,
            imageSrc: "clockinImg",
            buttonsState: { clockIn: true, clockOut: false, onBreak: false, offBreak: true }
        },
        2: {
            message: `${employeeName}さん、気分転換のために少し休憩しましょう。`,
            imageSrc: "onbreakImg",
            buttonsState: { clockIn: true, clockOut: true, onBreak: true, offBreak: false }
        },
        3: {
            message: `${employeeName}さん、残りの勤務時間、頑張っていきましょう。`,
            imageSrc: "offbreakImg",
            buttonsState: { clockIn: true, clockOut: false, onBreak: false, offBreak: true }
        },
        4: {
            message: `${employeeName}さん、今日の勤務、おつかれさまでした。`,
            imageSrc: "clockoutImg",
            buttonsState: { clockIn: true, clockOut: true, onBreak: true, offBreak: true }
        }
    };

    // 状態に応じたメッセージ、画像ソース、ボタン状態を適用
    const config = statusConfig[workStatus] || {};
    document.querySelector(".content__message").textContent = config.message || "";
    document.querySelector(".content__image img").src = document.getElementById("imageContainer").dataset[config.imageSrc] || "";
    setButtonsState(config.buttonsState || {});

    // 退勤打刻時にタイマー表示をリセット
    if (workStatus == 4) {
        document.getElementById("timerDisplay").textContent = '';
    }

    // アニメーション効果を再適用
    contentMessage.classList.add('fade-in'); // 新しいメッセージでアニメーションを再適用
    contentImage.classList.add('fade-in'); // 新しい画像でアニメーションを再適用
}

/**
 * 指定された状態に基づいてボタンの状態を設定する。
 * @param {Object} state - ボタンの状態を指定するオブジェクト
 */
function setButtonsState(state) {
    // 各ボタンの状態を設定
    document.getElementById("clockInButton").disabled = state.clockIn;
    document.getElementById("clockOutButton").disabled = state.clockOut;
    document.getElementById("onBreakButton").disabled = state.onBreak;
    document.getElementById("offBreakButton").disabled = state.offBreak;
}
