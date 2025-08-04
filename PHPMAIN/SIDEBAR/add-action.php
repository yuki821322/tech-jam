<?php
$filename = '../../CSV/task-data.csv';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $task_title = $_POST['title'] ?? '';
    $task_deadline = $_POST['deadline'] ?? '';
    $task_content = $_POST['content'] ?? '';

    // IDを自動生成（ランダムな一意ID）
    $id = uniqid();

    // CSVに追記
    if (($fp = fopen($filename, 'a')) !== false) {
        flock($fp, LOCK_EX);
        fputcsv($fp, [$id, $task_title, $task_deadline, $task_content]);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    // 登録後に戻る
    header("Location: add-task.php");
    exit;
}
