<?php
$csv_file = '../../CSV/task-data.csv';
$temp_file = '../../CSV/task-data-temp.csv';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $delete_id = $_POST['id'];
    $deleted = false;

    if (!file_exists($csv_file)) {
        echo "CSVファイルが存在しません。";
        exit;
    }

    $fp_read = fopen($csv_file, 'r');
    $fp_write = fopen($temp_file, 'w');

    if ($fp_read && $fp_write) {
        // ヘッダーをコピー
        $header = fgetcsv($fp_read);
        fputcsv($fp_write, $header);

        while (($row = fgetcsv($fp_read)) !== false) {
            if ($row[0] !== $delete_id) {
                fputcsv($fp_write, $row); // 削除対象でなければ書き込む
            } else {
                $deleted = true; // 削除成功フラグ
            }
        }

        fclose($fp_read);
        fclose($fp_write);

        // 削除成功時にファイルを上書き
        if ($deleted) {
            rename($temp_file, $csv_file);
        } else {
            unlink($temp_file); // 削除IDが見つからなかった場合はテンポラリ削除
        }
    } else {
        echo "ファイルを開くことができませんでした。";
        exit;
    }

    // 削除後リダイレクト
    header('Location: mytask.php');
    exit;
} else {
    echo "無効なリクエストです。";
    exit;
}

