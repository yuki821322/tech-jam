<?php
$filename = '../CSV/user_data.csv';
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

// 削除
$members = array_filter($members, function ($row) use ($id) {
    return $row[0] !== $id;
});

// 書き込み
if (($fp = fopen($filename, 'w')) !== false) {
    flock($fp, LOCK_EX);
    fputcsv($fp, ['ID', '名前', 'メールアドレス', '登録日']);
    foreach ($members as $row) {
        fputcsv($fp, $row);
    }
    flock($fp, LOCK_UN);
    fclose($fp);
}

header('Location: member_list.php');
exit;
