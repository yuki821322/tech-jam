<?php
$filename = '../../CSV/task-data.csv';
$id = $_GET['id'] ?? ($_POST['id'] ?? '');
$tasks = [];

// CSV読み込み
if (($fp = fopen($filename, 'r')) !== false) {
    flock($fp, LOCK_SH);
    $header = fgetcsv($fp);
    while ($row = fgetcsv($fp)) {
        $tasks[] = $row;
    }
    flock($fp, LOCK_UN);
    fclose($fp);
}

// 編集対象探す
$target = null;
if ($id !== '') {
    foreach ($tasks as $row) {
        if ($row[0] === $id) {
            $target = $row;
            break;
        }
    }
}

// 更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($tasks as &$row) {
        if ($row[0] === $_POST['id']) {
            $row[1] = $_POST['title'];
            $row[2] = $_POST['deadline'];
            $row[3] = $_POST['content'];
            break;
        }
    }
    unset($row);

    if (($fp = fopen($filename, 'w')) !== false) {
        flock($fp, LOCK_EX);
        fputcsv($fp, $header);
        foreach ($tasks as $row) {
            fputcsv($fp, $row);
        }
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    header('Location: mytask.php'); // 一覧に戻る
    exit;
}

$task_title = $target[1] ?? '';
$task_deadline = $target[2] ?? '';
$task_content = $target[3] ?? '';
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <title>タスク編集</title>
    <link rel="stylesheet" href="../../CSS/from/SIDEBARCSS/mytask-edit.css" />
</head>

<body>
    <div class="box">
        <h1>タスク編集</h1>
        <?php if ($target): ?>
            <form method="post">
                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                <p><span>タイトル</span><input type="text" name="title" value="<?= htmlspecialchars($task_title) ?>" required></p>
                <p><span>期限</span><input type="date" name="deadline" value="<?= htmlspecialchars($task_deadline) ?>" required></p>
                <p><span>内容</span><textarea name="content" rows="5" required><?= htmlspecialchars($task_content) ?></textarea></p>
                <p><button type="submit" class="button-30">更新</button></p>
            </form>
        <?php else: ?>
            <p>指定されたタスクが見つかりませんでした。</p>
        <?php endif; ?>
    </div>
</body>

</html>