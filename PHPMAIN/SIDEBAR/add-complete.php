<?php
// POSTデータを取得
$task_title = $_POST['task_title'] ?? '';
$task_deadline = $_POST['task_deadline'] ?? '';
$task_format_text = $_POST['task_format_text'] ?? '';
$task_content = $_POST['task_content'] ?? '';

// 利用形態の判定
$is_multi = ($task_format_text === 'マルチ');

// 保存先を分ける
if ($is_multi) {
    $csv_file = '../../CSV/jointtask.csv'; // マルチ用
} else {
    $csv_file = '../../CSV/task-data.csv'; // シングル用
}

// データ保存
if (($fp = fopen($csv_file, 'a')) !== false) {
    fputcsv($fp, [$task_title, $task_deadline, $task_format_text, $task_content]);
    fclose($fp);
}

// 終了後に add-task.php にリダイレクト
header('Location: add-task.php?success=1');
exit;
