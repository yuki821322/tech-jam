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
    <style>
        /* 全体の横幅を狭く調整 */
        .all {
            max-width: 1000px !important;
        }

        .task {
            max-width: 900px !important;
            margin: 0 auto;
        }

        .add-task-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #3498db 0%, #2ecc71 100%);
            color: white !important;
            text-decoration: none;
            padding: 20px 35px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.3);
            border: none;
            cursor: pointer;
            margin: 25px auto;
            display: block;
            width: fit-content;
            min-width: 200px;
        }

        .add-task-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(52, 152, 219, 0.4);
            background: linear-gradient(135deg, #2980b9 0%, #27ae60 100%);
            color: white !important;
        }

        .add-task-button::before {
            content: '+';
            font-size: 28px;
            font-weight: bold;
            margin-right: 10px;
            line-height: 1;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #64748b;
        }

        .empty-state p {
            font-size: 18px;
            margin-bottom: 25px;
            color: rgba(255, 255, 255, 0.8);
        }
    </style>
</head>

<body>
    <?php include '../PHPMAIN/header.php'; ?>

    <div class="all">
        <!-- 自分のタスク一覧 -->
        <div class="task">
            <div class="section-title">My Task</div>
            <div class="mytask-block">
                <?php if (empty($my_tasks)): ?>
                    <div class="empty-state">
                        <p>まだタスクがありません</p>
                        <a href="../PHPMAIN/SIDEBAR/add-task.php" class="add-task-button">タスクを追加</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($my_tasks as $index => $task): ?>
                        <?php $is_done = isset($task[3]) && $task[3] === '1'; ?>
                        <div class="mytask-item <?= $is_done ? 'done-task' : '' ?>"
                            onclick="toggleMyTask(<?= $index ?>)"
                            style="cursor: pointer;">
                            <strong><?= htmlspecialchars($task[1], ENT_QUOTES, 'UTF-8') ?></strong><br>
                            締切: <?= formatDate($task[2]) ?>
                        </div>
                    <?php endforeach; ?>
                    <a href="../PHPMAIN/SIDEBAR/add-task.php" class="add-task-button">タスクを追加</a>
                <?php endif; ?>
            </div>

            <!-- プロジェクト別マルチタスク -->
            <div class="section-title">Project Tasks</div>
            <div class="jointtask-block">
                <?php if (empty($projects)): ?>
                    <div class="jointtask-project">
                        <div class="empty-state">
                            <p>まだプロジェクトがありません</p>
                            <a href="../PHPMAIN/SIDEBAR/add-task.php" class="add-task-button">プロジェクトを追加</a>
                        </div>
                    </div>
                <?php else: ?>
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
                                <a href="../PHPMAIN/SIDEBAR/add-task.php" class="add-task-button">タスクを追加</a>
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
                                <a href="../PHPMAIN/SIDEBAR/add-task.php" class="add-task-button">タスクを追加</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="/tech-jam/JS/header.js"></script>
    <script>
        // 各プロジェクトのタスク数を保持
        let projectData = {};
        <?php if (!empty($projects)): ?>
            <?php $i = 0;
            $total = count($projects); ?>
            <?php foreach ($projects as $project_id => $project_title): ?>
                projectData['<?= htmlspecialchars($project_id, ENT_QUOTES, 'UTF-8') ?>'] = {
                    total: <?= $project_progress[$project_id]['total'] ?>,
                    done: <?= $project_progress[$project_id]['done'] ?>
                };
                <?php $i++; ?>
            <?php endforeach; ?>
        <?php endif; ?>

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

        function toggleMyTask(taskIndex) {
            const taskElements = document.querySelectorAll('.mytask-item');
            const taskElement = taskElements[taskIndex];

            if (!taskElement || taskElement.dataset.processing === 'true') {
                return;
            }

            const wasDone = taskElement.classList.contains('done-task');

            console.log('Toggling my task:', taskIndex, 'wasDone:', wasDone);

            // 処理中フラグを設定
            taskElement.dataset.processing = 'true';
            taskElement.style.pointerEvents = 'none';
            taskElement.style.opacity = '0.7';

            // UI を即座に更新
            if (wasDone) {
                taskElement.classList.remove('done-task');
            } else {
                taskElement.classList.add('done-task');
            }

            // サーバーに状態を送信
            const formData = new FormData();
            formData.append('task_index', taskIndex);
            formData.append('done', wasDone ? '0' : '1');

            fetch('mytask-check.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('My task response status:', response.status);

                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`HTTP ${response.status}: ${text}`);
                        });
                    }

                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    }

                    return {
                        success: true
                    };
                })
                .then(data => {
                    console.log('My task success:', data);
                })
                .catch(error => {
                    console.error('Error updating my task:', error);

                    // エラーが発生した場合は元に戻す
                    if (wasDone) {
                        taskElement.classList.add('done-task');
                    } else {
                        taskElement.classList.remove('done-task');
                    }

                    alert('タスクの更新に失敗しました。ページを再読み込みしてください。\nエラー: ' + error.message);
                })
                .finally(() => {
                    // 処理完了後にフラグを解除
                    taskElement.dataset.processing = 'false';
                    taskElement.style.pointerEvents = '';
                    taskElement.style.opacity = '';
                });
        }

        function toggleTask(projectId, taskIndex) {
            const taskElement = event.target.closest('.jointtask-item');

            // 既に処理中の場合は無視
            if (taskElement.dataset.processing === 'true') {
                return;
            }

            const wasDone = taskElement.classList.contains('done-task');

            console.log('Toggling task:', projectId, taskIndex, 'wasDone:', wasDone);

            // 処理中フラグを設定
            taskElement.dataset.processing = 'true';
            taskElement.style.pointerEvents = 'none';
            taskElement.style.opacity = '0.7';

            // UI を即座に更新
            if (wasDone) {
                taskElement.classList.remove('done-task');
                projectData[projectId].done--;
            } else {
                taskElement.classList.add('done-task');
                projectData[projectId].done++;
            }

            // プログレスバーを更新
            updateProjectProgressBar(projectId);

            // サーバーに状態を送信
            const formData = new FormData();
            formData.append('project_id', projectId);
            formData.append('task_index', taskIndex);

            if (!wasDone) {
                formData.append('done', '1');
            }

            fetch('task-check.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Response status:', response.status);

                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`HTTP ${response.status}: ${text}`);
                        });
                    }

                    // JSON レスポンスの場合
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    }

                    // 成功（リダイレクトレスポンスの場合）
                    return {
                        success: true
                    };
                })
                .then(data => {
                    console.log('Success:', data);

                    if (data.backup_file) {
                        console.log('Backup created:', data.backup_file);
                    }
                })
                .catch(error => {
                    console.error('Error updating task:', error);

                    // エラーが発生した場合は元に戻す
                    if (wasDone) {
                        taskElement.classList.add('done-task');
                        projectData[projectId].done++;
                    } else {
                        taskElement.classList.remove('done-task');
                        projectData[projectId].done--;
                    }

                    updateProjectProgressBar(projectId);

                    // ユーザーにエラーを通知
                    alert('タスクの更新に失敗しました。ページを再読み込みしてください。\nエラー: ' + error.message);
                })
                .finally(() => {
                    // 処理完了後にフラグを解除
                    taskElement.dataset.processing = 'false';
                    taskElement.style.pointerEvents = '';
                    taskElement.style.opacity = '';
                });
        }
    </script>
</body>

</html>