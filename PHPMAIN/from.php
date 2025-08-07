<?php
session_start();
if (!isset($_SESSION['csv_name'])) {
    header("Location: ../PHP/index.php");
    exit;
}

$csv_name = $_SESSION['csv_name'];
$csv_file = '../CSV/task-data.csv';
$project_csv = '../CSV/project.csv';
$joint_csv = '../CSV/jointtask.csv';

// UTF-8 BOM除去関数
function removeBom($str)
{
    return preg_replace('/^\xEF\xBB\xBF/', '', $str);
}

// 日付フォーマット（例: 2025-08-07 → 2025年8月7日）
function formatDate($dateString)
{
    $date = DateTime::createFromFormat('Y-m-d', $dateString);
    return $date ? $date->format('Y年n月j日') : htmlspecialchars($dateString, ENT_QUOTES, 'UTF-8');
}

// 自分のタスク読み込み
$my_tasks = [];
if (file_exists($csv_file) && ($fp = fopen($csv_file, 'r')) !== false) {
    $header = fgetcsv($fp);
    if (!empty($header)) $header[0] = removeBom($header[0]);

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

// プロジェクト一覧読み込み
$projects = [];
if (file_exists($project_csv) && ($fp = fopen($project_csv, 'r')) !== false) {
    $header = fgetcsv($fp);
    if (!empty($header)) $header[0] = removeBom($header[0]);

    while (($row = fgetcsv($fp)) !== false) {
        if (count($row) >= 2) {
            $projects[$row[0]] = $row[1];
        }
    }
    fclose($fp);
}

// マルチタスク読み込み
$tasks_by_project = [];
if (file_exists($joint_csv) && ($fp = fopen($joint_csv, 'r')) !== false) {
    $header = fgetcsv($fp);
    if (!empty($header)) $header[0] = removeBom($header[0]);

    while (($row = fgetcsv($fp)) !== false) {
        if (count($row) < 5) continue;

        $project_id = removeBom($row[0]);
        $row = array_pad($row, 6, '0'); // 6列そろえる
        $tasks_by_project[$project_id][] = [
            'title' => $row[1],
            'deadline' => $row[2],
            'subtasks' => array_filter(explode('|', $row[3])),
            'creator' => $row[4],
            'done' => $row[5],
        ];
    }
    fclose($fp);
}

// プログレスバー計算
$total_tasks = $done_tasks = 0;
foreach ($tasks_by_project as $project_tasks) {
    foreach ($project_tasks as $task) {
        $total_tasks++;
        if ($task['done'] === '1') $done_tasks++;
    }
}
$progress_percent = $total_tasks > 0 ? round(($done_tasks / $total_tasks) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ようこそ</title>
    <link rel="stylesheet" href="../CSS/from/from.css?v=5.0">
    <link rel="stylesheet" href="../CSS/from/header.css">
</head>

<body>
    <?php include '../PHPMAIN/header.php'; ?>

    <div class="all">
        <!-- 自分のタスク一覧 -->
        <div class="task">
            <div class="section-title">My Task</div>
            <div class="mytask-block">
                <?php foreach ($my_tasks as $task): ?>
                    <div class="mytask-item">
                        <strong><?= htmlspecialchars($task[1], ENT_QUOTES, 'UTF-8') ?></strong><br>
                        締切: <?= formatDate($task[2]) ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- プロジェクト別マルチタスク -->
            <div class="section-title">Project Tasks</div>
            <div class="jointtask-block">
                <?php foreach ($projects as $project_id => $project_title): ?>
                    <div class="jointtask-project">
                        <h2><?= htmlspecialchars($project_title, ENT_QUOTES, 'UTF-8') ?></h2>
                        <?php $project_tasks = $tasks_by_project[$project_id] ?? []; ?>

                        <?php if (empty($project_tasks)): ?>
                            <p class="no-tasks">このプロジェクトにはまだマルチタスクがありません。</p>
                        <?php else: ?>
                            <?php foreach ($project_tasks as $index => $task): ?>
                                <?php $is_done = $task['done'] === '1'; ?>
                                <div class="jointtask-item <?= $is_done ? 'done-task' : '' ?>">
                                    <form method="POST" action="task-check.php">
                                        <input type="hidden" name="project_id" value="<?= htmlspecialchars($project_id, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="task_index" value="<?= $index ?>">
                                        <input type="checkbox" name="done" value="1" <?= $is_done ? 'checked' : '' ?> onchange="this.form.submit()">
                                        <span>
                                            <strong><?= htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') ?></strong><br>
                                            担当: <?= htmlspecialchars($task['creator'], ENT_QUOTES, 'UTF-8') ?><br>
                                            期限: <?= formatDate($task['deadline']) ?>
                                        </span>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- プログレスバー -->
        <div class="progress">
            <h1>プログレスバー</h1>
            <progress id="file" max="100" value="<?= $progress_percent ?>"><?= $progress_percent ?>%</progress>
            <p><?= $progress_percent ?>%</p>
        </div>
    </div>

    <script src="/tech-jam/JS/header.js"></script>
</body>

</html>