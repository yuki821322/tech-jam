<?php
$erased_filename = '../../CSV/erased.csv';
$deleted_members = [];
$header = [];

if (($fp = fopen($erased_filename, 'r')) !== false) {
    flock($fp, LOCK_SH);
    $header = fgetcsv($fp);  // ヘッダー行取得
    while ($row = fgetcsv($fp)) {
        $deleted_members[] = $row;
    }
    flock($fp, LOCK_UN);
    fclose($fp);
} else {
    echo '削除済みファイルがありません。';
    exit;
}

$gender_list = [
    0 => '未設定',
    1 => '男性',
    2 => '女性',
    9 => 'その他'
];
?>
<!-- http://localhost:8888/tech-jam/LIST/DELETE/erased.php -->

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>削除済みメンバー一覧</title>
</head>

<body>
    <h1>削除済みメンバー一覧</h1>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <?php foreach ($header as $col): ?>
                    <th><?= htmlspecialchars($col) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($deleted_members as $member): ?>
                <tr>
                    <?php foreach ($member as $index => $field): ?>
                        <td>
                            <?php if ($index === 4): ?>
                                <?= htmlspecialchars($gender_list[(int)$field] ?? '不明') ?>
                            <?php else: ?>
                                <?= htmlspecialchars($field) ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <td>
                        <form action="restore.php" method="post">
                            <input type="hidden" name="restore_id" value="<?= htmlspecialchars($member[0]) ?>">
                            <button type="submit">復元</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
</body>

</html>