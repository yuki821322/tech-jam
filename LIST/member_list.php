<?php
$filename = '../CSV/user_data.csv';

// ファイル存在チェック
if (!file_exists($filename)) {
    echo 'ファイルが存在しません。';
    exit;
}

// ファイルを読み込んで配列に格納
$members = [];
if (($fp = fopen($filename, 'r')) !== false) {
    flock($fp, LOCK_SH);
    // ヘッダー行をスキップ
    $header = fgetcsv($fp);
    while ($row = fgetcsv($fp)) {
        $members[] = $row;
    }
    flock($fp, LOCK_UN);
    fclose($fp);
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>メンバーリスト</title>
    <link rel="stylesheet" href="../CSS/member_list.css">
</head>

<body>
    <div class="container">
        <h1>メンバーリスト</h1>
        <?php if (count($members) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>名前</th>
                        <th>登録日</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($member[0], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($member[1], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($member[2], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="not-found">メンバーが存在しません。</p>
        <?php endif; ?>
    </div>
</body>

</html>