/**
 * スタッフ勤怠情報が読み込まれた後に実行されるイベントリスナー。
 * URLから年月日を取得し、日付を更新するためのイベントハンドラを設定する。
 */
document.addEventListener("DOMContentLoaded", () => {
    // 年と月を取得または初期設定
    const { year, month, day } = getDateFromParams();

    /**
     * URLのクエリパラメータから年と月と日を取得するか、取得できない場合は今日の年月日を返す。
     *
     * @returns {Object} 年(year)と月(month)と日（day）を含むオブジェクト
     */
    function getDateFromParams() {
        const params = new URLSearchParams(window.location.search);
        const today = new Date();
        return {
            year: params.has("year")
                ? parseInt(params.get("year"))
                : today.getFullYear(),
            month: params.has("month")
                ? parseInt(params.get("month")) - 1
                : today.getMonth(),
            day: params.has("day")
                ? parseInt(params.get("day"))
                : today.getDate(),
        };
    }

    /**
     * 与えられた日の変化量に基づいて日付を更新し、新しいURLにリダイレクトする。
     *
     * @param {number} delta - 日の変化量（負の値で減少、正の値で増加）
     */
    function updateDate(delta) {
        // 新しい年、月、日を取得
        let newDate = new Date(year, month, day + delta);
        let newYear = newDate.getFullYear();
        let newMonth = newDate.getMonth();
        let newDay = newDate.getDate();

        // 新しいURLにリダイレクト
        const newUrl = generateUpdatedUrl(newYear, newMonth + 1, newDay);
        window.location.href = newUrl;
    }

    /**
     * 指定された年と月と日もとに、新しいクエリ文字列を含むURLを生成する。
     *
     * @param {number} year - 更新後の年
     * @param {number} month - 更新後の月
     * @param {number} day - 更新後の日
     * @returns {string} 新しいURL
     */
    function generateUpdatedUrl(year, month, day) {
        const baseUrl = window.location.href.split("?")[0];
        return `${baseUrl}?year=${year}&month=${month.toString().padStart(2, "0")}&day=${day.toString().padStart(2, "0")}`;
    }

    // 現在の年月を表示
    document.getElementById("currentDate").textContent = `${year}-${(month + 1).toString().padStart(2, "0")}-${day.toString().padStart(2, "0")}`;

    // 「次の月」「前の月」ボタンへのイベントリスナー設定
    document
        .getElementById("nextMonth")
        .addEventListener("click", () => updateDate(1));
    document
        .getElementById("prevMonth")
        .addEventListener("click", () => updateDate(-1));
});
