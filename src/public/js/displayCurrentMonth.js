/**
 * 日付一覧が読み込まれた後に実行されるイベントリスナー。
 * URLから年と月を取得し、カレンダーの表示を更新するためのイベントハンドラを設定する。
 */
document.addEventListener("DOMContentLoaded", () => {
    // 年と月を取得または初期設定
    const { year, month } = getYearAndMonthFromParams();

    /**
     * URLのクエリパラメータから年と月を取得するか、取得できない場合は今日の年月を返す。
     *
     * @returns {Object} 年(year)と月(month)を含むオブジェクト
     */
    function getYearAndMonthFromParams() {
        const params = new URLSearchParams(window.location.search);
        const today = new Date();
        return {
            year: params.has("year")
                ? parseInt(params.get("year"))
                : today.getFullYear(),
            month: params.has("month")
                ? parseInt(params.get("month")) - 1
                : today.getMonth(),
        };
    }

    /**
     * 与えられた月の変化量に基づいて年月を更新し、新しいURLにリダイレクトする。
     *
     * @param {number} delta - 月の変化量（負の値で減少、正の値で増加）
     */
    function updateDate(delta) {
        let newMonth = month + delta;
        let newYear = year;

        // 月の値を調整し、年を繰り越す処理
        if (newMonth < 0) {
            newMonth = 11;
            newYear--;
        } else if (newMonth > 11) {
            newMonth = 0;
            newYear++;
        }

        // 新しいURLにリダイレクト
        const newUrl = generateUpdatedUrl(newYear, newMonth + 1);
        window.location.href = newUrl;
    }

    /**
     * 指定された年と月をもとに、新しいクエリ文字列を含むURLを生成する。
     *
     * @param {number} year - 更新後の年
     * @param {number} month - 更新後の月
     * @returns {string} 新しいURL
     */
    function generateUpdatedUrl(year, month) {
        const baseUrl = window.location.href.split("?")[0];
        return `${baseUrl}?year=${year}&month=${month
            .toString()
            .padStart(2, "0")}`;
    }

    // 現在の年月を表示
    document.getElementById("currentDate").textContent = `${year}-${(month + 1)
        .toString()
        .padStart(2, "0")}`;

    // 「次の月」「前の月」ボタンへのイベントリスナー設定
    document
        .getElementById("nextMonth")
        .addEventListener("click", () => updateDate(1));
    document
        .getElementById("prevMonth")
        .addEventListener("click", () => updateDate(-1));
});
