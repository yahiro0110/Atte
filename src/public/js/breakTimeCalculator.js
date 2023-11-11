/**
 * ページ読み込み後に初期設定を行う。
 */
document.addEventListener("DOMContentLoaded", function () {
    // 休憩時間追加ボタンの参照を取得
    const addButton = document.getElementById("add-break-time");
    // 休憩時間追加ボタンにクリックイベントリスナーを設定
    addButton.addEventListener("click", addBreakTime);

    // 休憩時間計算の初期化
    initializeTimeCalculations();

    /**
     * 新しい休憩時間エリアを追加する関数。
     */
    function addBreakTime() {
        // 既存の休憩時間エリアの数を取得
        const breakCount = document.querySelectorAll(
            ".content__form-inputsubarea-time"
        ).length;

        // 新しい休憩時間エリアのHTML要素を作成
        const newBreakTime = document.createElement("div");
        newBreakTime.className = "content__form-inputsubarea-time";
        newBreakTime.innerHTML = `
            <label for="">${breakCount + 1}回目<br>(新規)</label>
            <input type="text" value="00:00:00" />
            <span>-</span>
            <input type="text" value="00:00:00" />
            <button type="button">削除</button>
        `;

        // 削除ボタンにクリックイベントリスナーを設定
        newBreakTime.querySelector("button").onclick = function () {
            // 休憩時間エリアを削除
            newBreakTime.remove();
            // 合計時間を更新
            updateTotalTime();
        };

        // 新しい休憩時間エリアをページに追加
        document
            .querySelector(".content__form-inputsubarea")
            .appendChild(newBreakTime);

        // 新しい休憩時間エリアにイベントリスナーを追加
        addInputEventListeners(newBreakTime);
        // 合計時間を更新
        updateTotalTime();
    }

    /**
     * 休憩時間エリアの初期化を行う関数。
     */
    function initializeTimeCalculations() {
        // すべての休憩時間エリアにイベントリスナーを追加
        document
            .querySelectorAll(".content__form-inputsubarea-time")
            .forEach((div) => {
                addInputEventListeners(div);
            });
    }

    /**
     * 指定された休憩時間エリアにイベントリスナーを追加する関数。
     * @param {HTMLElement} div - 休憩時間エリアのdiv要素。
     */
    function addInputEventListeners(div) {
        // 休憩時間エリア内のすべてのテキスト入力にイベントリスナーを設定
        div.querySelectorAll('input[type="text"]').forEach((input) => {
            input.addEventListener("input", updateTotalTime);
        });
    }

    /**
     * 合計休憩時間を計算し、表示する関数。
     */
    function updateTotalTime() {
        // 計算された合計休憩時間を表示
        document.querySelector(".calculatetimes").value =
            calculateDifferences();
    }
});

/**
 * 指定された時間文字列を秒単位に変換する関数。
 * @param {string} time - HH:MM:SS 形式の時間文字列。
 * @returns {number} 秒単位の合計時間。
 */
function timeToSeconds(time) {
    // 時間文字列を分解し、秒単位に変換
    const [hours, minutes, seconds] = time.split(":").map(parseFloat);
    return hours * 3600 + minutes * 60 + seconds;
}

/**
 * すべての休憩時間差分を計算し、合計を HH:MM:SS 形式の文字列で返す関数。
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
