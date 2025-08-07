<?php
// ========== jointtask.php (マルチタスク一覧表示) ==========

// UTF-8 BOM除去関数
function removeBom($str)
{
    return preg_replace('/^\xEF\xBB\xBF/', '', $str);
}

// ユーザーデータを読み込む
$user_csv = '../../CSV/user_data.csv';
$users = [];

if (file_exists($user_csv) && ($fp = fopen($user_csv, 'r')) !== false) {
    $header = fgetcsv($fp);
    if (!empty($header)) {
        $header[0] = removeBom($header[0]);
    }

    while (($row = fgetcsv($fp)) !== false) {
        if (count($row) >= 2) {
            $users[$row[0]] = $row[1];
        }
    }
    fclose($fp);
}

// プロジェクト一覧を取得
$project_csv = '../../CSV/project.csv';
$projects = [];

if (file_exists($project_csv) && ($fp = fopen($project_csv, 'r')) !== false) {
    $header = fgetcsv($fp);
    if (!empty($header)) {
        $header[0] = removeBom($header[0]);
    }

    while (($row = fgetcsv($fp)) !== false) {
        if (count($row) >= 2) {
            $projects[$row[0]] = $row[1];
        }
    }
    fclose($fp);
}

// マルチタスク一覧を読み込む
$task_csv = '../../CSV/jointtask.csv';
$tasks_by_project = [];
$task_counter = 0;

if (file_exists($task_csv) && ($fp = fopen($task_csv, 'r')) !== false) {
    $header = fgetcsv($fp);
    if (!empty($header)) {
        $header[0] = removeBom($header[0]);
    }

    while (($row = fgetcsv($fp)) !== false) {
        // 最低5列必要、6列目（done）はオプション
        if (count($row) < 5) continue;

        // 各要素のBOM除去とtrim処理
        foreach ($row as $key => $value) {
            $row[$key] = trim(removeBom($value));
        }

        // 列数が足りない場合は空文字で埋める（done列対応）
        while (count($row) < 6) {
            $row[] = '';
        }

        $project_id = $row[0];
        $creator_id = $row[4];
        $creator_name = isset($users[$creator_id]) ? $users[$creator_id] : $creator_id;

        $task = [
            'id' => $task_counter++,
            'title' => $row[1],
            'deadline' => $row[2],
            'subtasks' => array_filter(explode('|', $row[3])),
            'creator' => $creator_name,
            'creator_id' => $creator_id,
            'project_id' => $project_id,
            'done' => $row[5] // done状態を追加
        ];

        if (!isset($tasks_by_project[$project_id])) {
            $tasks_by_project[$project_id] = [];
        }
        $tasks_by_project[$project_id][] = $task;
    }
    fclose($fp);
}

// 日付のフォーマット関数
function formatDate($dateString)
{
    $date = DateTime::createFromFormat('Y-m-d', $dateString);
    if ($date) {
        return $date->format('Y年m月d日');
    }
    return htmlspecialchars($dateString);
}

// 締切が近いかどうかをチェック
function isDeadlineNear($dateString)
{
    $deadline = DateTime::createFromFormat('Y-m-d', $dateString);
    $today = new DateTime();
    $today->setTime(0, 0, 0);

    if ($deadline) {
        $diff = $today->diff($deadline);
        return ($diff->days <= 3 && $deadline >= $today);
    }
    return false;
}

// 締切が過ぎているかチェック
function isOverdue($dateString)
{
    $deadline = DateTime::createFromFormat('Y-m-d', $dateString);
    $today = new DateTime();
    $today->setTime(0, 0, 0);

    if ($deadline) {
        return $deadline < $today;
    }
    return false;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>マルチタスク一覧</title>
    <link rel="stylesheet" href="/tech-jam/CSS/from/header.css">
    <link rel="stylesheet" href="/tech-jam/CSS/from/SIDEBARCSS/jointtask.css">
    <style>
        .task-card.done-task {
            opacity: 0.6;
            text-decoration: line-through;
        }
    </style>
</head>

<body>
    <?php include '../header.php'; ?>
    <h1>プロジェクト別マルチタスク一覧</h1>

    <?php foreach ($projects as $project_id => $project_title): ?>
        <section class="project-section">
            <h2><?php echo htmlspecialchars($project_title, ENT_QUOTES, 'UTF-8'); ?></h2>

            <?php if (empty($tasks_by_project[$project_id])): ?>
                <p class="no-task">このプロジェクトにはまだマルチタスクがありません。</p>
            <?php else: ?>
                <div class="task-list">
                    <?php foreach ($tasks_by_project[$project_id] as $index => $task): ?>
                        <?php $is_done = $task['done'] === '1'; ?>
                        <div class="task-card <?= $is_done ? 'done-task' : '' ?>"
                            onclick="toggleTask('<?= htmlspecialchars($project_id, ENT_QUOTES, 'UTF-8') ?>', <?= $index ?>)"
                            style="cursor: pointer;">
                            <div class="task-header">
                                <strong><?php echo htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                <span class="deadline">（締切: <?php echo htmlspecialchars($task['deadline'], ENT_QUOTES, 'UTF-8'); ?>）</span>
                                <div class="creator">担当: <?php echo htmlspecialchars($task['creator'], ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>

                            <ul class="subtask-list">
                                <?php foreach ($task['subtasks'] as $subtask): ?>
                                    <li><?php echo htmlspecialchars($subtask, ENT_QUOTES, 'UTF-8'); ?></li>
                                <?php endforeach; ?>
                            </ul>

                            <div class="task-actions">
                                <a href="jointtask-edit.php?id=<?php echo urlencode($project_id . '|' . $task['title']); ?>">編集</a> |
                                <a href="jointtask-delete.php?id=<?php echo urlencode($project_id . '|' . $task['title']); ?>" onclick="return confirm('本当に削除しますか？');">削除</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>

    <script src="/tech-jam/JS/header.js"></script>
    <script>
        function toggleTask(projectId, taskIndex) {
            const taskElement = event.target.closest('.task-card');
            const wasDone = taskElement.classList.contains('done-task');

            // 取り消し線のトグル
            if (wasDone) {
                taskElement.classList.remove('done-task');
            } else {
                taskElement.classList.add('done-task');
            }

            // サーバーに状態を送信（非同期）
            const formData = new FormData();
            formData.append('project_id', projectId);
            formData.append('task_index', taskIndex);

            if (!wasDone) {
                formData.append('done', '1');
            }

            fetch('../../PHP/task-check.php', {
                method: 'POST',
                body: formData
            }).catch(error => {
                console.error('Error:', error);
                // エラーが発生した場合は元に戻す
                if (wasDone) {
                    taskElement.classList.add('done-task');
                } else {
                    taskElement.classList.remove('done-task');
                }
            });
        }
    </script>
</body>

</html>