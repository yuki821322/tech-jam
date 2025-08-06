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
$tasks_by_project = [];

if (file_exists($task_csv) && ($fp = fopen($task_csv, 'r')) !== false) {
    $header = fgetcsv($fp);
    while (($row = fgetcsv($fp)) !== false) {
        if (count($row) < 5) continue;

        $project_id = $row[0];
        $task = [
            'title' => $row[1],
            'deadline' => $row[2],
            'subtasks' => explode('|', $row[3]),
            'creator' => $row[4]
        ];

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

<head>
    <meta charset="UTF-8">
    <title>ようこそ</title>
    <link rel="stylesheet" href="../CSS/from/from.css">
    <link rel="stylesheet" href="../CSS/from/header.css">
</head>

<body>
    <?php include '../PHPMAIN/header.php'; ?>

    <div class="all">
        <!-- タスク一覧 -->
        <div class="task">
            <!-- 自分のタスク一覧 -->
            <div class="section-title">My Task</div>
            <div class="mytask-block">
                <?php foreach ($data as $task): ?>
                    <?php
                    if (count($task) < 4) continue;

                    $task_title = $task[1];
                    $task_deadline = $task[2];

                    $date = DateTime::createFromFormat('Y-m-d', $task_deadline);
                    $formattedDate = $date ? $date->format('Y年n月j日') : htmlspecialchars($task_deadline);
                    ?>
                    <div class="mytask-item">
                        <strong><?= htmlspecialchars($task_title) ?></strong><br>
                        締切: <?= $formattedDate ?>
                    </div>
                <?php endforeach; ?>
            </div>


            <!-- プロジェクトごとのマルチタスク一覧 -->
            <div class="section-title">Project Tasks</div>
            <div class="jointtask-block">
                <?php foreach ($projects as $project_id => $project_title): ?>
                    <div class="jointtask-project">
                        <h2><?= htmlspecialchars($project_title, ENT_QUOTES, 'UTF-8') ?></h2>
                        <?php if (empty($tasks_by_project[$project_id])): ?>
                            <p class="no-tasks">このプロジェクトにはまだマルチタスクがありません。</p>
                        <?php else: ?>
                            <?php foreach ($tasks_by_project[$project_id] as $task): ?>
                                <div class="jointtask-item">
                                    <strong><?= htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') ?></strong><br>
                                    作成者: <?= htmlspecialchars($task['creator'], ENT_QUOTES, 'UTF-8') ?><br>
                                    締切: <?= htmlspecialchars($task['deadline'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 右側：進捗バー -->
        <div class="progress">
            <h1>プログレスバー</h1>
            <progress id="file" max="100" value="70">70%</progress>
        </div>
    </div>

    <script src="../JS/header.js"></script>
</body>

</html>