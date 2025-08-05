<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/tech-jam/CSS/from/header.css">
    <link rel="stylesheet" href="/tech-jam/CSS/from/SIDEBARCSS/add-task.css">
    <link rel="stylesheet" href="/tech-jam/CSS/from/SIDEBARCSS/add-project.css">
    <title>タスク追加</title>
</head>

<body>
    <?php include '../header.php'; ?>
    <div class="project-form-container">
        <h2>プロジェクト追加</h2>

        <form action="save-project.php" method="POST">
            <input type="text" id="project_title" name="project_title" required>
            <button type="submit">追加</button>
        </form>
    </div>
    <script src="/tech-jam/JS/header.js"></script>
</body>

</html>