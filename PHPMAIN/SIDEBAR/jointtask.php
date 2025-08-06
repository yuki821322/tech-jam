<?php
// ========== jointtask.php (マルチタスク一覧表示) ==========

// プロジェクト一覧を取得
$project_csv = '../../CSV/project.csv';
$projects = [];

if (file_exists($project_csv) && ($fp = fopen($project_csv, 'r')) !== false) {
    $header = fgetcsv($fp); // ヘッダー行をスキップ

    // UTF-8 BOM が入ってる場合、強制的に削除
    if (!empty($header)) {
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
    }

    while (($row = fgetcsv($fp)) !== false) {
        if (count($row) >= 2) {
            $projects[$row[0]] = $row[1]; // ID => タイトル の連想配列に
        }
    }
    fclose($fp);
}

// マルチタスク一覧を読み込む
$task_csv = '../../CSV/jointtask.csv';
$tasks_by_project = [];
$task_counter = 0; // 各タスクにユニークなIDを付与

if (file_exists($task_csv) && ($fp = fopen($task_csv, 'r')) !== false) {
    $header = fgetcsv($fp);

    // UTF-8 BOM が入ってる場合、強制的に削除
    if (!empty($header)) {
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
    }

    while (($row = fgetcsv($fp)) !== false) {
        if (count($row) < 5) continue;

        // UTF-8 BOM が入ってる場合、ここでも念のため除去
        $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);

        $project_id = $row[0];
        $task = [
            'id' => $task_counter++, // ユニークなタスクID
            'title' => $row[1],
            'deadline' => $row[2],
            'subtasks' => array_filter(explode('|', $row[3])), // 空の要素を除去
            'creator' => $row[4],
            'project_id' => $project_id
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
                    <?php foreach ($tasks_by_project[$project_id] as $task): ?>
                        <div class="task-card">
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
</body>

</html>