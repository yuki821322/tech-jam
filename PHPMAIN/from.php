<?php
session_start();
if (!isset($_SESSION['csv_name'])) {
    header("Location: ../PHP/index.php");
    exit;
}
$csv_name = $_SESSION['csv_name'];
$csv_file = '../CSV/task-data.csv';

$header = [];
$data = [];

if (file_exists($csv_file) && ($fp = fopen($csv_file, 'r')) !== false) {
    $header = fgetcsv($fp);
    while (($row = fgetcsv($fp)) !== false) {
        $data[] = $row;
    }
    fclose($fp);
} else {
    echo "ファイルが存在しないか、読み込みできません。";
    exit;
}

$project_csv = '../CSV/project.csv';
$projects = [];

if (file_exists($project_csv) && ($fp = fopen($project_csv, 'r')) !== false) {
    while (($row = fgetcsv($fp)) !== false) {
        if (count($row) >= 2) {
            $projects[$row[0]] = $row[1];
        }
    }
    fclose($fp);
}

$task_csv = '../CSV/jointtask.csv';
$tasks_by_project = []; // project_id => [task1, task2, ...]

if (file_exists($task_csv) && ($fp = fopen($task_csv, 'r')) !== false) {
    $header = fgetcsv($fp); // ヘッダー読み飛ばし
    while (($row = fgetcsv($fp)) !== false) {
        if (count($row) < 5) continue;

        $project_id = $row[0];
        $task = [
            'title' => $row[1],
            'deadline' => $row[2],
            'subtasks' => explode('|', $row[3]),
            'creator' => $row[4]
        ];

        // グループ化
        if (!isset($tasks_by_project[$project_id])) {
            $tasks_by_project[$project_id] = [];
        }
        $tasks_by_project[$project_id][] = $task;
    }
    fclose($fp);
}
?>

<!DOCTYPE html>
<html lang="ja">

<!-- http://localhost:8888/tech-jam/PHPMAIN/from.php -->

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../CSS/from/from.css">
    <link rel="stylesheet" href="../CSS/from/header.css">
    <title>ようこそ</title>
</head>

<body>

    <?php include '../PHPMAIN/header.php'; ?>

    <!-- メインエリア -->
    <div class="all">
        <div class="task">
            <h1>タスク管理</h1>
            <div class="mytask-list">
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
            </div>
            <div class="jointtask-list">
                <?php foreach ($projects as $project_id => $project_title): ?>
                    <section style="margin-bottom: 2em;">
                        <h2 style="color: navy;"><?php echo htmlspecialchars($project_title, ENT_QUOTES, 'UTF-8'); ?></h2>

                        <?php if (empty($tasks_by_project[$project_id])): ?>
                            <p style="color: gray;">このプロジェクトにはまだマルチタスクがありません。</p>
                        <?php else: ?>
                            <ul>
                                <?php foreach ($tasks_by_project[$project_id] as $task): ?>
                                    <li>
                                        <strong><?php echo htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                        （締切: <?php echo htmlspecialchars($task['deadline'], ENT_QUOTES, 'UTF-8'); ?>）
                                        <br>作成者: <?php echo htmlspecialchars($task['creator'], ENT_QUOTES, 'UTF-8'); ?>
                                        <ul>
                                            <?php foreach ($task['subtasks'] as $subtask): ?>
                                                <li><?php echo htmlspecialchars($subtask, ENT_QUOTES, 'UTF-8'); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </section>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="progress">
            <h1>プログレスバー</h1>
            <progress id="file" max="100" value="70">70%</progress>
        </div>
    </div>
    <script src="../JS/header.js"></script>

</body>

</html>