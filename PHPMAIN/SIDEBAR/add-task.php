<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    echo "<pre>";
    var_dump($_POST);
    echo "</pre>";
}

$csv_file = '../../CSV/project.csv';

// プロジェクト情報読み込み
$projects = [];
if (file_exists($csv_file) && ($fp = fopen($csv_file, 'r')) !== false) {
    while (($row = fgetcsv($fp)) !== false) {
        if (count($row) >= 2) {
            $projects[] = ['id' => $row[0], 'title' => $row[1]];
        }
    }
    fclose($fp);
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/tech-jam/CSS/from/header.css">
    <link rel="stylesheet" href="/tech-jam/CSS/from/SIDEBARCSS/add-task.css">
    <title>タスク追加</title>
</head>

<body>
    <?php include '../header.php'; ?>
    <div class="add-wrap">
        <h1>Task File</h1>

        <!-- タブボタン -->
        <div class="tab-buttons">
            <button class="tab-button active" data-target="mytask-form">Mytask</button>
            <button class="tab-button" data-target="teamtask-form">Teamtask</button>
        </div>

        <!-- Mytask フォーム -->
        <div class="form-section active" id="mytask-form">
            <form action="add-action.php" method="post">
                <p><input class="title" type="text" name="title" required placeholder="タイトル"></p>
                <p><input class="deadline" type="date" name="deadline" required placeholder="YYYY/MM/DD"></p>
                <p><textarea class="content" name="content" rows="3" placeholder="内容" required></textarea></p>
                <input type="hidden" name="task_type" value="single">
                <p><input type="submit" value="追加"></p>
            </form>
        </div>

        <!-- Teamtask フォーム -->
        <div class="form-section" id="teamtask-form">
            <form action="joint.php" method="post">
                <p>
                    <select name="project_id" required>
                        <option value="">プロジェクトを選択</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?php echo htmlspecialchars($project['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <p><input class="title" type="text" name="multi_title" required placeholder="マルチタスクのタイトル"></p>
                <p><input class="deadline" type="date" name="multi_deadline" required placeholder="YYYY/MM/DD"></p>

                <div id="subtasks">
                    <div class="subtask">
                        <textarea name="subtasks[]" rows="3" placeholder="内容" required></textarea>
                    </div>
                </div>

                <input type="hidden" name="task_type" value="multi">
                <p><input type="submit" value="マルチタスク追加"></p>
            </form>
        </div>
    </div>
    <script src="/tech-jam/JS/header.js"></script>
    <script src="/tech-jam/JS/add-task.js"></script>
</body>

</html>