<?php
session_start();

$csv_file = '../CSV/jointtask.csv';

// POST データの検証
if (!isset($_POST['project_id']) || !isset($_POST['task_index'])) {
    header('Location: from.php');
    exit;
}

$project_id = trim($_POST['project_id']);
$task_index = (int)$_POST['task_index'];
$is_done = isset($_POST['done']) ? '1' : '0';

// UTF-8 BOM除去関数
function removeBom($str)
{
    return preg_replace('/^\xEF\xBB\xBF/', '', $str);
}

$rows = [];
if (file_exists($csv_file) && ($fp = fopen($csv_file, 'r')) !== false) {
    while (($row = fgetcsv($fp)) !== false) {
        // 各要素のBOM除去とtrim処理
        foreach ($row as $key => $value) {
            $row[$key] = trim(removeBom($value));
        }
        $rows[] = $row;
    }
    fclose($fp);
} else {
    header('Location: from.php');
    exit;
}

// 行ごとに確認して一致したら完了フラグ更新
$project_task_index = 0;
$found = false;

foreach ($rows as &$row) {
    // 列数が足りない場合は空文字で埋める
    while (count($row) < 6) {
        $row[] = '';
    }

    if ($row[0] === $project_id) {
        if ($project_task_index === $task_index) {
            $row[5] = $is_done;
            $found = true;
            break;
        }
        $project_task_index++;
    }
}

// ファイルに書き戻し（UTF-8 BOM付き）
if ($found && ($fp = fopen($csv_file, 'w')) !== false) {
    // UTF-8 BOMを書き込み
    fwrite($fp, "\xEF\xBB\xBF");
    foreach ($rows as $row) {
        fputcsv($fp, $row);
    }
    fclose($fp);
}

header('Location: from.php');
exit;
