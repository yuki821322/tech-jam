<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    echo "<pre>";
    var_dump($_POST);
    echo "</pre>";
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
        <h1>Task file</h1>
        <form action="add-action.php" method="post">
            <p><input class="title" type="text" id="title" name="title" required placeholder="タイトル"></p>
            <p><input class="deadline" type="date" id="deadline" name="deadline" required placeholder="YYYY/MM/DD"></p>
            <p><textarea class="content" name="content" rows="3" cols="20" wrap="hard" required placeholder="内容"></textarea></p>
            <p><input type="submit" value="追加"></p>
        </form>
    </div>

    <script src="/tech-jam/JS/header.js"></script>
</body>

</html>