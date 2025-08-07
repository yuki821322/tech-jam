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

        // 各要素のBOM除去とtrim処理
        foreach ($row as $key => $value) {
            $row[$key] = trim(removeBom($value));
        }

        // 列数が足りない場合は空文字で埋める
        while (count($row) < 6) {
            $row[] = '';
        }

        $project_id = $row[0];
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
    <link rel="stylesheet" href="../CSS/from/from.css">
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
                                <div class="jointtask-item <?= $is_done ? 'done-task' : '' ?>"
                                    onclick="toggleTask('<?= htmlspecialchars($project_id, ENT_QUOTES, 'UTF-8') ?>', <?= $index ?>)"
                                    style="cursor: pointer;">
                                    <strong><?= htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') ?></strong><br>
                                    担当: <?= htmlspecialchars($task['creator'], ENT_QUOTES, 'UTF-8') ?><br>
                                    期限: <?= formatDate($task['deadline']) ?>
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
            <progress id="progress-bar" max="100" value="<?= $progress_percent ?>"><?= $progress_percent ?>%</progress>
            <p id="progress-text"><?= $progress_percent ?>% (<?= $done_tasks ?>/<?= $total_tasks ?>)</p>
        </div>
    </div>

    <script src="/tech-jam/JS/header.js"></script>
    <script>
        let totalTasks = <?= $total_tasks ?>;
        let doneTasks = <?= $done_tasks ?>;

        function updateProgressBar() {
            const progressPercent = totalTasks > 0 ? Math.round((doneTasks / totalTasks) * 100) : 0;
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');

            progressBar.value = progressPercent;
            progressText.textContent = progressPercent + '% (' + doneTasks + '/' + totalTasks + ')';
        }

        function toggleTask(projectId, taskIndex) {
            const taskElement = event.target.closest('.jointtask-item');
            const wasDone = taskElement.classList.contains('done-task');

            // 取り消し線のトグル
            if (wasDone) {
                taskElement.classList.remove('done-task');
                doneTasks--; // 完了数を減らす
            } else {
                taskElement.classList.add('done-task');
                doneTasks++; // 完了数を増やす
            }

            // プログレスバーを即座に更新
            updateProgressBar();

            // サーバーに状態を送信（非同期）
            const formData = new FormData();
            formData.append('project_id', projectId);
            formData.append('task_index', taskIndex);

            if (!wasDone) {
                formData.append('done', '1');
            }

            fetch('task-check.php', {
                method: 'POST',
                body: formData
            }).catch(error => {
                console.error('Error:', error);
                // エラーが発生した場合は元に戻す
                if (wasDone) {
                    taskElement.classList.add('done-task');
                    doneTasks++;
                } else {
                    taskElement.classList.remove('done-task');
                    doneTasks--;
                }
                updateProgressBar();
            });
        }
    </script>
</body>

</html>