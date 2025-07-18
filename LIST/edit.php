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

// 該当メンバーを探す
$target = null;
foreach ($members as $row) {
    if ($row[0] === $id) {
        $target = $row;
        break;
    }
}

// POST処理（保存）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($members as &$row) {
        if ($row[0] === $_POST['id']) {
            $row[1] = $_POST['name'];
            $row[2] = $_POST['email'];
            break;
        }
    }

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
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../CSS//list/edit.css">
    <title>編集画面</title>
</head>

<body>
    <div class="box">
        <h1>会員情報の編集</h1>
        <?php if ($target): ?>
            <form method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($target[0]); ?>">
                <p>名前：<input type="text" name="name" value="<?php echo htmlspecialchars($target[1]); ?>"></p>
                <p>メール：<input type="email" name="email" value="<?php echo htmlspecialchars($target[2]); ?>"></p>
                <p><button type="submit">更新</button></p>
            </form>
        <?php else: ?>
            <p>指定されたIDは見つかりませんでした。</p>
        <?php endif; ?>
    </div>
</body>

</html>