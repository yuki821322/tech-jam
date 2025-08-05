<?php
session_start();
if (!isset($_SESSION['csv_name'])) {
    header("Location: ../PHP/index.php");
    exit;
}

$csv_name = $_SESSION['csv_name']; // ログインユーザー名
$csv_file = '../../CSV/jointtask.csv'; // マルチタスク共通ファイル

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = $_POST['project_id'] ?? '';
    $title = $_POST['multi_title'] ?? '';
    $deadline = $_POST['multi_deadline'] ?? '';
    $subtasks = $_POST['subtasks'] ?? [];

    if (!$project_id || !$title || !$deadline || empty($subtasks)) {
        echo "すべての項目を入力してください。";
        exit;
    }

    $subtask_string = implode('|', array_map('trim', $subtasks));

    if (($fp = fopen($csv_file, 'a')) !== false) {
        if (filesize($csv_file) === 0) {
            // 初回ヘッダー
            fputcsv($fp, ['project_id', 'title', 'deadline', 'subtasks', 'creator']);
        }

        fputcsv($fp, [$project_id, $title, $deadline, $subtask_string, $csv_name]);
        fclose($fp);
    }

    header('Location: jointtask.php'); // 一覧ページに戻す
    exit;
}
