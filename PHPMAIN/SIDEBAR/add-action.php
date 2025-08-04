<?php


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $task_title = $_POST['title'] ?? '';
    $task_deadline = $_POST['deadline'] ?? '';
    $task_content = $_POST['content'] ?? '';

    // CSVファイルに保存
    $file = fopen('../../CSV/task-data.csv', 'a');
    if ($file !== false) {
        fputcsv($file, [
            $task_title,
            $task_deadline,
            $task_format_text,
            $task_content
        ]);
        fclose($file);
    } else {
        echo '<p>CSVファイルを開けませんでした。</p>';
    }

    echo '<a href="add-task.php">戻る</a>';
} else {
    echo 'POSTでアクセスしてください。';
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>確認画面</title>
</head>

<body>
    <table border="1">
        <th>タスク名</th>
        <td><?php echo htmlspecialchars($task_title, ENT_QUOTES, 'UTF-8'); ?></td>
        <th>期限</th>
        <td><?php echo htmlspecialchars($task_deadline, ENT_QUOTES, 'UTF-8'); ?></td>
        <th>内容</th>
        <td><?php echo htmlspecialchars($task_content, ENT_QUOTES, 'UTF-8'); ?></td>
    </table>
    <form action="add-complete.php" method="post">
        <input type="hidden" name="task_title" value="<?php echo htmlspecialchars($task_title, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="task_deadline" value="<?php echo htmlspecialchars($task_deadline, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="task_content" value="<?php echo htmlspecialchars($task_content, ENT_QUOTES, 'UTF-8'); ?>">
        <p>
            <input class="task-add-submit" type="submit" value="追加">
        </p>
    </form>
</body>

</html>