/**
 * ページ読み込み完了時に初期設定を行うイベントリスナー。
 */
document.addEventListener("DOMContentLoaded", function () {
    // 休憩時間追加ボタンの設定
    setupAddBreakTimeButton();
    // 休憩時間エリアの初期設定
    initializeTimeCalculations();
});

/**
 * 休憩時間追加ボタンの設定を行う。
 */
function setupAddBreakTimeButton() {
    // 休憩時間追加ボタン要素の取得
    const addButton = document.getElementById("add-break-time");
    // クリックイベントリスナーの追加
    addButton.addEventListener("click", addBreakTime);
}

/**
 * すべての休憩時間エリアにイベントリスナー（入力時、削除ボタンクリック時）を設定する。
 */
function initializeTimeCalculations() {
    document
        .querySelectorAll(".content__form-inputsubarea-time")
        .forEach(setupBreakTimeArea);
}

/**
 * 休憩時間エリアに対する設定を行う。
 * @param {HTMLElement} div - 休憩時間エリアのdiv要素
 */
function setupBreakTimeArea(div) {
    addInputEventListeners(div); // 入力イベントリスナーの設定
    setupDeleteButton(div); // 削除ボタンの設定
}

/**
 * 休憩時間エリアを追加する。
 */
function addBreakTime() {
    const breakCount = document.querySelectorAll(
        ".content__form-inputsubarea-time"
    ).length; // 既存の休憩時間エリアの数
    const newBreakTime = createBreakTimeArea(breakCount + 1); // 新しい休憩時間エリアの作成
    newBreakTime.querySelector("button").onclick = function () {
        newBreakTime.remove(); // 削除ボタンのクリック時の処理
        updateTotalTime(); // 合計時間の更新
    };
    document
        .querySelector(".content__form-inputsubarea")
        .appendChild(newBreakTime); // 新しいエリアの追加
    setupBreakTimeArea(newBreakTime); // 新しいエリアの設定
    updateTotalTime(); // 合計時間の更新
}

/**
 * 休憩時間エリアのHTML要素を作成する。
 * @param {number} breakNumber - 休憩回数
 * @returns {HTMLElement} 休憩時間エリアのdiv要素
 */
function createBreakTimeArea(breakNumber) {
    const div = document.createElement("div");
    div.className = "content__form-inputsubarea-time";
    div.innerHTML = `
        <input type="hidden" name="breaktime_ids[]" value="0">
        <label for="">${breakNumber}回目<br>(新規)</label>
        <input type="text" name="breaktime_start_time[]" value="00:00:00" />
        <span>-</span>
        <input type="text" name="breaktime_end_time[]" value="00:00:00" />
        <button type="button">削除</button>
    `; // HTMLコンテンツの設定
    return div;
}

/**
 * 休憩時間エリアの削除ボタンにイベントリスナーを設定する。
 * @param {HTMLElement} div - 休憩時間エリアのdiv要素
 */
function setupDeleteButton(div) {
    const deleteButton = div.querySelector(".delete-btn"); // 削除ボタンの選択
    deleteButton.addEventListener("click", function () {
        deleteBreakTime(div, this.value); // クリック時の処理
    });
}

/**
 * 休憩時間エリアを削除する。
 * @param {HTMLElement} div - 削除対象の休憩時間エリアのdiv要素
 * @param {string} breaktimeId - 削除対象の休憩時間のID
 */
function deleteBreakTime(div, breaktimeId) {
    if (confirm("本当に削除しますか？")) {
        var csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"); // CSRFトークンの取得
        fetch("/breaktime/delete/" + breaktimeId, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
                "Content-Type": "application/json",
            },
        })
            .then(handleResponse) // レスポンス処理
            .then(() => {
                div.remove(); // div要素の削除
                updateTotalTime(); // 合計時間の更新
            })
            .catch((error) => console.error("Error:", error)); // エラー処理
    }
}

/**
 * レスポンスを処理する。
 * @param {Response} response - fetchからのレスポンス
 * @returns {Promise} レスポンスのJSON
 * @throws エラー時に例外を投げる
 */
function handleResponse(response) {
    if (!response.ok) {
        throw new Error("Network response was not ok");
    }
    return response.json(); // JSONの解析
}

/**
 * 休憩時間エリアの入力フィールドにイベントリスナーを追加する。
 * @param {HTMLElement} div - 休憩時間エリアのdiv要素
 */
function addInputEventListeners(div) {
    div.querySelectorAll('input[type="text"]').forEach((input) => {
        input.addEventListener("input", updateTotalTime); // 入力イベントリスナーの追加
    });
}

/**
 * 合計休憩時間を計算し、表示する。
 */
function updateTotalTime() {
    // 計算された合計休憩時間を表示
    document.querySelector(".calculatetimes").value = calculateDifferences();
}

/**
 * 指定された時間文字列を秒単位に変換する。
 * @param {string} time - HH:MM:SS 形式の時間文字列。
 * @returns {number} 秒単位の合計時間。
 */
function timeToSeconds(time) {
    // 時間文字列を分解し、秒単位に変換
    const [hours, minutes, seconds] = time.split(":").map(parseFloat);
    return hours * 3600 + minutes * 60 + seconds;
}

/**
 * すべての休憩時間差分を計算し、合計を HH:MM:SS 形式の文字列で返す。
 * @returns {string} 合計時間を表す HH:MM:SS 形式の文字列。
 */
function calculateDifferences() {
    // 合計秒数を計算
    let totalSeconds = 0;
    document
        .querySelectorAll(".content__form-inputsubarea-time")
        .forEach((div) => {
            // 各休憩時間エリアの開始時間と終了時間を取得
            const inputs = div.querySelectorAll('input[type="text"]');
            const startTime = timeToSeconds(inputs[0].value);
            const endTime = timeToSeconds(inputs[1].value);
            // 差分を計算
            const difference = endTime - startTime;
            // 合計に加算
            totalSeconds += difference;
        });

    // 合計秒数をHH:MM:SS形式に変換
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;
    return `${hours.toString().padStart(2, "0")}:${minutes
        .toString()
        .padStart(2, "0")}:${seconds.toString().padStart(2, "0")}`;
}
