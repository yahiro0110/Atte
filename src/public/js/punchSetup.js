/**
 * 打刻が読み込まれた後に実行されるイベントリスナー。
 * フォーム要素のボタンにAjaxでPOST送信を実行するためのイベントハンドラを設定する。
 */
document.addEventListener("DOMContentLoaded", function () {
    setupEventListeners();
});

/**
 * さまざまなフォーム要素にsubmitイベントリスナーを追加する。
 */
function setupEventListeners() {
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

    const config = statusConfig[workStatus] || {};
    document.querySelector(".content__message").textContent = config.message || "";
    document.querySelector(".content__image img").src = document.getElementById("imageContainer").dataset[config.imageSrc] || "";
    setButtonsState(config.buttonsState || {});
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
