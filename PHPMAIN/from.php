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

// 各プロジェクトのプログレス計算
$project_progress = [];
foreach ($projects as $project_id => $project_title) {
    $project_tasks = $tasks_by_project[$project_id] ?? [];
    $total = count($project_tasks);
    $done = 0;

    foreach ($project_tasks as $task) {
        if ($task['done'] === '1') $done++;
    }

    $progress_percent = $total > 0 ? round(($done / $total) * 100) : 0;

    $project_progress[$project_id] = [
        'total' => $total,
        'done' => $done,
        'percent' => $progress_percent
    ];
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

                        <!-- 各プロジェクトのプログレスバー -->
                        <div class="project-progress">
                            <progress id="progress-bar-<?= htmlspecialchars($project_id, ENT_QUOTES, 'UTF-8') ?>"
                                max="100"
                                value="<?= $project_progress[$project_id]['percent'] ?>">
                                <?= $project_progress[$project_id]['percent'] ?>%
                            </progress>
                            <span id="progress-text-<?= htmlspecialchars($project_id, ENT_QUOTES, 'UTF-8') ?>">
                                <?= $project_progress[$project_id]['percent'] ?>%
                                (<?= $project_progress[$project_id]['done'] ?>/<?= $project_progress[$project_id]['total'] ?>)
                            </span>
                        </div>

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
    </div>

    <script src="/tech-jam/JS/header.js"></script>
    <script>
        // 各プロジェクトのタスク数を保持
        let projectData = {
            <?php foreach ($projects as $project_id => $project_title): ?> '<?= htmlspecialchars($project_id, ENT_QUOTES, 'UTF-8') ?>': {
                    total: <?= $project_progress[$project_id]['total'] ?>,
                    done: <?= $project_progress[$project_id]['done'] ?>
                }
                <?= end($projects) === $project_title ? '' : ',' ?>
            <?php endforeach; ?>
        };

        function updateProjectProgressBar(projectId) {
            const data = projectData[projectId];
            const progressPercent = data.total > 0 ? Math.round((data.done / data.total) * 100) : 0;
            const progressBar = document.getElementById('progress-bar-' + projectId);
            const progressText = document.getElementById('progress-text-' + projectId);

            if (progressBar && progressText) {
                progressBar.value = progressPercent;
                progressText.textContent = progressPercent + '% (' + data.done + '/' + data.total + ')';
            }
        }

        function toggleTask(projectId, taskIndex) {
            const taskElement = event.target.closest('.jointtask-item');
            const wasDone = taskElement.classList.contains('done-task');

            // 取り消し線のトグル
            if (wasDone) {
                taskElement.classList.remove('done-task');
                projectData[projectId].done--; // 完了数を減らす
            } else {
                taskElement.classList.add('done-task');
                projectData[projectId].done++; // 完了数を増やす
            }

            // そのプロジェクトのプログレスバーを即座に更新
            updateProjectProgressBar(projectId);

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
                    projectData[projectId].done++;
                } else {
                    taskElement.classList.remove('done-task');
                    projectData[projectId].done--;
                }
                updateProjectProgressBar(projectId);
            });
        }
    </script>
</body>

</html>