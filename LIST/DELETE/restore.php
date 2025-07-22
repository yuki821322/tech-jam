<?php
$erased_filename = '../../CSV/erased.csv';
$active_filename = '../../CSV/user_data.csv';

if (!isset($_POST['restore_id'])) {
    echo '復元IDが指定されていません。';
    exit;
}

$restore_id = $_POST['restore_id'];
$deleted_members = [];
$header = [];
$restored_member = null;

// 削除済みCSVを読み込む
if (($fp = fopen($erased_filename, 'r')) !== false) {
    flock($fp, LOCK_SH);
    $header = fgetcsv($fp);
    while ($row = fgetcsv($fp)) {
        if ($row[0] === $restore_id) {
            $restored_member = $row;
        } else {
            $deleted_members[] = $row;
        }
    }
    flock($fp, LOCK_UN);
    fclose($fp);
}

if ($restored_member === null) {
    echo '指定されたメンバーが見つかりません。';
    exit;
}

// ★ 削除日時を除外
array_pop($restored_member);

// user_data.csv に追加
if (($fp = fopen($active_filename, 'a')) !== false) {
    flock($fp, LOCK_EX);
    fputcsv($fp, $restored_member);
    flock($fp, LOCK_UN);
    fclose($fp);
}

// erased.csv を上書き（復元された行を除く）
if (($fp = fopen($erased_filename, 'w')) !== false) {
    flock($fp, LOCK_EX);
    fputcsv($fp, $header);
    foreach ($deleted_members as $row) {
        fputcsv($fp, $row);
    }
    flock($fp, LOCK_UN);
    fclose($fp);
}

// ★ 復元後に自動で削除一覧へ戻る
header('Location: erased.php');
exit;
