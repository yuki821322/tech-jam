<?php
$filename = '../../CSV/user_data.csv';
$erased_filename = '../../CSV/erased.csv';
$id = $_GET['id'] ?? '';
$members = [];

// 読み込み
if (($fp = fopen($filename, 'r')) !== false) {
    flock($fp, LOCK_SH);
    $header = fgetcsv($fp);
    while ($row = fgetcsv($fp)) {
        $members[] = $row;
    }
    flock($fp, LOCK_UN);
    fclose($fp);
}

$deleted_rows = [];
// 削除（$idと一致する行を取り出しつつ、$membersから除く）
$members = array_filter($members, function ($row) use ($id, &$deleted_rows) {
    if ($row[0] === $id) {
        $deleted_rows[] = $row;
        return false; // 削除対象行
    }
    return true;
});

// 元ファイル書き込み（削除後の残りのデータ）
if (($fp = fopen($filename, 'w')) !== false) {
    flock($fp, LOCK_EX);
    fputcsv($fp, $header);
    foreach ($members as $row) {
        fputcsv($fp, $row);
    }
    flock($fp, LOCK_UN);
    fclose($fp);
}

// 削除したデータをerased.csvに追記（削除日時を追加）
if (!empty($deleted_rows)) {
    $now = date('Y-m-d H:i:s');

    // erased.csvのヘッダー（存在しなければ書く）
    if (!file_exists($erased_filename)) {
        $fp = fopen($erased_filename, 'w');
        fputcsv($fp, array_merge($header, ['削除日時']));
        fclose($fp);
    }

    if (($fp = fopen($erased_filename, 'a')) !== false) {
        flock($fp, LOCK_EX);
        foreach ($deleted_rows as $row) {
            fputcsv($fp, array_merge($row, [$now]));
        }
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}

header('Location: ../member_list.php');
exit;
