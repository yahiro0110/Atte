/**
 * 新規登録が読み込まれた後に実行されるイベントリスナー。
 * 各input要素を走査し、特定の条件に基づいてその境界線の色を変更する。
 * （条件内容：すべてのinput要素に対して、次の要素が'content__form-error'クラスを持つ場合）
 * この条件を満たす場合、input要素の境界線の色が赤色（#DA4040）に変更される。
 */
document.querySelectorAll('input').forEach(function (input) {
    // 現在のinput要素の次の要素を取得
    var nextElement = input.nextElementSibling;
    // 次の要素が存在し、かつ'content__form-error'クラスを持つ場合
    if (nextElement && nextElement.classList.contains('content__form-error')) {
        // input要素の境界線の色を赤色に設定
        input.style.borderColor = '#DA4040';
    }
});
