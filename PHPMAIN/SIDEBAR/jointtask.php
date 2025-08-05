<?php
// プロジェクト一覧を取得
$project_csv = '../../CSV/project.csv';
$projects = [];

if (file_exists($project_csv) && ($fp = fopen($project_csv, 'r')) !== false) {
    while (($row = fgetcsv($fp)) !== false) {
        if (count($row) >= 2) {
            $projects[$row[0]] = $row[1]; // ID => タイトル の連想配列に
        }
    }
    fclose($fp);
}

// マルチタスク一覧を読み込む
$task_csv = '../../CSV/jointtask.csv';
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
    <script src="/tech-jam/JS/header.js"></script>
</body>

</html>