<?php
$csv_file = '../CSV/jointtask.csv';

$project_id = $_POST['project_id'];
$task_index = (int)$_POST['task_index'];
$is_done = isset($_POST['done']) ? '1' : '0';

$rows = [];
if (($fp = fopen($csv_file, 'r')) !== false) {
    while (($row = fgetcsv($fp)) !== false) {
        $rows[] = $row;
    }
    fclose($fp);
}

// 行ごとに確認して一致したら完了フラグ更新
$project_task_index = 0;
foreach ($rows as &$row) {
    if (count($row) < 6) {
        $row = array_pad($row, 6, '0'); // 6列まで埋める（足りなければ '0'）
    }

    if ($row[0] === $project_id) {
        if ($project_task_index === $task_index) {
            $row[5] = $is_done;
            break;
        }
        $project_task_index++;
    }
}

if (($fp = fopen($csv_file, 'w')) !== false) {
    foreach ($rows as $row) {
        fputcsv($fp, $row);
    }
    fclose($fp);
}

header('Location: from.php');
exit;
