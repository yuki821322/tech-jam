<?php
$csv_file = '../../CSV/project.csv'; // CSVファイルの場所

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $project_title = trim($_POST['project_title'] ?? '');

    if ($project_title !== '') {
        $id = uniqid(); // ユニークなID生成
        $date = date("Y-m-d");

        // CSVに書き込むデータ（ID, タイトル, 登録日）
        $new_row = [$id, $project_title, $date];

        if (($fp = fopen($csv_file, 'a')) !== false) {
            flock($fp, LOCK_EX); // ロック
            fputcsv($fp, $new_row);
            flock($fp, LOCK_UN); // アンロック
            fclose($fp);
        }

        // 登録後にリダイレクト（例：一覧ページや元ページに戻る）
        header("Location: add-project.php?success=1");
        exit;
    } else {
        echo "タイトルが空です。";
    }
} else {
    echo "POSTで送信してください。";
}
