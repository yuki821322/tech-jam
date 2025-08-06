<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>チェックテスト</title>
    <style>
        .task-item {
            padding: 10px;
            border: 1px solid #aaa;
            margin: 10px 0;
        }

        .task-item.done .content {
            text-decoration: line-through;
            opacity: 0.6;
        }
    </style>
</head>

<body>
    <?php
    $tasks = [
        ['title' => '買い物に行く', 'done' => true],
        ['title' => '宿題をする', 'done' => false],
    ];
    foreach ($tasks as $task):
        $done_class = $task['done'] ? 'done' : '';
    ?>
        <div class="task-item <?= $done_class ?>">
            <span class="content"><?= htmlspecialchars($task['title']) ?></span>
        </div>
    <?php endforeach; ?>
</body>

</html>