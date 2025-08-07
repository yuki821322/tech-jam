<?php
session_start();

$csv_file = '../CSV/task-data.csv';
$backup_file = '../CSV/task-data_backup_' . date('Y-m-d_H-i-s') . '.csv';

// POST データの検証
if (!isset($_POST['task_index'])) {
    http_response_code(400);
    exit('Missing required parameters');
}

$task_index = (int)$_POST['task_index'];
$is_done = isset($_POST['done']) ? $_POST['done'] : '0';

// UTF-8 BOM除去関数
function removeBom($str)
{
    return preg_replace('/^\xEF\xBB\xBF/', '', $str);
}

// CSVファイルの存在確認
if (!file_exists($csv_file)) {
    http_response_code(404);
    exit('CSV file not found');
}

// バックアップを作成
if (!copy($csv_file, $backup_file)) {
    http_response_code(500);
    exit('Failed to create backup');
}

// CSVファイルを読み込み
$rows = [];
$fp = fopen($csv_file, 'r');
if ($fp === false) {
    http_response_code(500);
    exit('Cannot read CSV file');
}

while (($row = fgetcsv($fp)) !== false) {
    // 空行をスキップ
    if (empty(array_filter($row))) {
        continue;
    }

    // 各要素のBOM除去とtrim処理
    foreach ($row as $key => $value) {
        $row[$key] = trim(removeBom($value));
    }

    // 4列に統一（done列を含む）
    while (count($row) < 4) {
        $row[] = '';
    }

    $rows[] = $row;
}
fclose($fp);

// 対象タスクを更新
if ($task_index >= 0 && $task_index < count($rows)) {
    $rows[$task_index][3] = $is_done;
} else {
    http_response_code(404);
    exit('Task not found');
}

// 一時ファイルに書き込み、成功したら元ファイルを置き換え
$temp_file = $csv_file . '.tmp';
$fp = fopen($temp_file, 'w');
if ($fp === false) {
    http_response_code(500);
    exit('Cannot create temporary file');
}

// UTF-8 BOMを書き込み
fwrite($fp, "\xEF\xBB\xBF");

// 全行を書き込み
$write_success = true;
foreach ($rows as $row) {
    if (fputcsv($fp, $row) === false) {
        $write_success = false;
        break;
    }
}

fclose($fp);

if (!$write_success) {
    unlink($temp_file);
    http_response_code(500);
    exit('Failed to write data');
}

// 一時ファイルを元ファイルに置き換え
if (!rename($temp_file, $csv_file)) {
    unlink($temp_file);
    http_response_code(500);
    exit('Failed to save changes');
}

// 成功時のレスポンス
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'updated_row' => $task_index,
    'backup_file' => basename($backup_file)
]);
exit;
