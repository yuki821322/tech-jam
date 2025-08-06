<?php
// ========== save-project.php (プロジェクト保存処理) ==========

$csv_file = '../../CSV/project.csv'; // CSVファイルの場所

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $project_title = trim($_POST['project_title'] ?? '');

    if ($project_title !== '') {
        $id = uniqid(); // ユニークなID生成
        $date = date("Y-m-d");
        $is_created = 'false'; // 新規なのでまだタスクなし

        // CSVに書き込むデータ（ID, タイトル, 登録日, is_created）
        $new_row = [$id, $project_title, $date, $is_created];

        // すでにヘッダーがあるかチェック
        $need_header = true;
        if (file_exists($csv_file) && filesize($csv_file) > 0) {
            $fp_check = fopen($csv_file, 'r');
            $first_line = fgetcsv($fp_check);
            fclose($fp_check);

            if ($first_line !== false) {
                $clean_first_line = array_map('trim', $first_line);
                // BOMがついてたら削除
                $clean_first_line[0] = preg_replace('/^\xEF\xBB\xBF/', '', $clean_first_line[0]);

                if ($clean_first_line === ['project_id', 'project_title', 'created_date', 'is_created']) {
                    $need_header = false;
                }
            }
        }

        // 書き込み
        if (($fp = fopen($csv_file, 'a')) !== false) {
            flock($fp, LOCK_EX); // ロック

            // 必要ならヘッダー行を追加（BOMなしで）
            if ($need_header) {
                fputcsv($fp, ['project_id', 'project_title', 'created_date', 'is_created']);
            }

            fputcsv($fp, $new_row);
            flock($fp, LOCK_UN); // アンロック
            fclose($fp);
        }

        // 登録後にリダイレクト
        header("Location: add-task.php?success=1");
        exit;
    } else {
        echo "タイトルが空です。";
    }
} else {
    echo "POSTで送信してください。";
}
