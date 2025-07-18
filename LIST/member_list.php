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


<!-- http://localhost:8888/tech-jam/LIST/member_list.php -->

<head>
    <meta charset="UTF-8">
    <title>メンバーリスト</title>
    <link rel="stylesheet" href="../CSS//list/member_list.css">
</head>

<body>
    <div class="container">
        <h1>会員詳細</h1>
        <?php if (count($members) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>名前</th>
                        <th>メールアドレス</th>
                        <th>登録日</th>
                        <th>パスワード</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($member[0], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($member[1], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($member[2], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($member[3], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($member[5], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo urlencode($member[0]); ?>">編集</a>
                                <a href="delete.php?id=<?php echo urlencode($member[0]); ?>" onclick="return confirm('本当に削除しますか？');">削除</a>
                            </td>
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