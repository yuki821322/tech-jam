<?php
// ファイルパス
$csv_file = '../../CSV/jointtask.csv';

// データ読み込み
$data = [];
if (file_exists($csv_file) && ($fp = fopen($csv_file, 'r')) !== false) {
    while (($row = fgetcsv($fp)) !== false) {
        $data[] = $row;
    }
    fclose($fp);
} else {
    echo "ファイルが存在しないか、読み込みできません。";
}

// デバッグ表示
echo "<h1>jointtask.csv の内容（マルチタスク）</h1>";
echo "<pre>";
var_dump($data);
echo "</pre>";
