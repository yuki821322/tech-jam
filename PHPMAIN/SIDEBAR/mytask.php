<?php
$csv_file = '../../CSV/task-data.csv';

$header = [];
$data = [];

if (file_exists($csv_file) && ($fp = fopen($csv_file, 'r')) !== false) {
    $header = fgetcsv($fp); // ヘッダー行
    while (($row = fgetcsv($fp)) !== false) {
        $data[] = $row;
    }
    fclose($fp);
} else {
    echo "ファイルが存在しないか、読み込みできません。";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/tech-jam/CSS/from/header.css">
    <link rel="stylesheet" href="/tech-jam/CSS/from/SIDEBARCSS/mytask.css">
    <title>My Multi Tasks</title>
</head>

<body>
    <?php include '../header.php'; ?>
    <div class="task-list">
        <h1>My Multi Tasks</h1>
        <?php if (count($data) > 0): ?>
            <table class="task-table">
                <thead>
                    <tr>
                        <th>｜タイトル</th>
                        <th>｜期限</th>
                        <th>｜内容</th>
                        <th>｜操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $task): ?>
                        <?php
                        if (count($task) < 4) {
                            continue; // 必須の列が足りない行はスキップ
                        }

                        $task_id = $task[0];
                        $task_title = $task[1];
                        $task_deadline = $task[2];
                        $task_content = $task[3];

                        $date = DateTime::createFromFormat('Y-m-d', $task_deadline);
                        $formattedDate = $date ? $date->format('Y年n月j日') : htmlspecialchars($task_deadline);
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($task_title) ?></td>
                            <td><?= $formattedDate ?></td>
                            <td><?= nl2br(htmlspecialchars($task_content)) ?></td>
                            <td class="task-actions">
                                <form action="mytask-edit.php" method="get" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($task_id) ?>">
                                    <button type="submit">編集</button>
                                </form>
                                <form action="mytask-delete.php" method="post" style="display:inline;" onsubmit="return confirm('本当に削除しますか？');">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($task_id) ?>">
                                    <button type="submit">削除</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>現在、表示できるマルチタスクはありません。</p>
        <?php endif; ?>
    </div>

    <script src="/tech-jam/JS/header.js"></script>
</body>

</html>