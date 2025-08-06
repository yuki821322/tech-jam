<?php
session_start();
if (!isset($_SESSION['csv_name'])) {
    header("Location: ../PHP/index.php");
    exit;
}

$csv_name = $_SESSION['csv_name'];
$csv_file = '../CSV/task-data.csv';

// 日付のフォーマット関数
function formatDate($dateString)
{
    $date = DateTime::createFromFormat('Y-m-d', $dateString);
    return $date ? $date->format('Y年n月j日') : htmlspecialchars($dateString);
}

// 締切が近いかチェック
function isDeadlineNear($dateString)
{
    $deadline = DateTime::createFromFormat('Y-m-d', $dateString);
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    return $deadline && $deadline >= $today && $today->diff($deadline)->days <= 3;
}

// 締切が過ぎているかチェック
function isOverdue($dateString)
{
    $deadline = DateTime::createFromFormat('Y-m-d', $dateString);
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    return $deadline && $deadline < $today;
}

// ▼ 自分のタスク読み込み
$my_tasks = [];
if (file_exists($csv_file) && ($fp = fopen($csv_file, 'r')) !== false) {
    $header = fgetcsv($fp);
    if (!empty($header)) {
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
    }
    while (($row = fgetcsv($fp)) !== false) {
        if (count($row) >= 3) {
            $my_tasks[] = $row;
        }
    }
    fclose($fp);
} else {
    echo "ファイルが存在しないか、読み込みできません。";
    exit;
}

// ▼ プロジェクト読み込み
$project_csv = '../CSV/project.csv';
$projects = [];

if (file_exists($project_csv) && ($fp = fopen($project_csv, 'r')) !== false) {
    $header = fgetcsv($fp);
    if (!empty($header)) {
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
    }
    while (($row = fgetcsv($fp)) !== false) {
        if (count($row) >= 2) {
            $projects[$row[0]] = $row[1];
        }
    }
    fclose($fp);
}

// ▼ マルチタスク読み込み
$joint_csv = '../CSV/jointtask.csv';
$tasks_by_project = [];

if (file_exists($joint_csv) && ($fp = fopen($joint_csv, 'r')) !== false) {
    $header = fgetcsv($fp);
    if (!empty($header)) {
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
    }
    while (($row = fgetcsv($fp)) !== false) {
        if (count($row) < 5) continue;

        $project_id = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]); // 念のためBOM除去
        $task = [
            'title' => $row[1],
            'deadline' => $row[2],
            'subtasks' => array_filter(explode('|', $row[3])),
            'creator' => $row[4]
        ];
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
                <?php foreach ($my_tasks as $task): ?>
                    <?php
                    $task_title = $task[1];
                    $task_deadline = $task[2];
                    ?>
                    <div class="mytask-item">
                        <strong><?= htmlspecialchars($task_title) ?></strong><br>
                        締切: <?= formatDate($task_deadline) ?>
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
                                    担当: <?= htmlspecialchars($task['creator'], ENT_QUOTES, 'UTF-8') ?><br>
                                    期限: <?= htmlspecialchars($task['deadline'], ENT_QUOTES, 'UTF-8') ?>
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

    <script src="/tech-jam/JS/header.js"></script>
</body>

</html>