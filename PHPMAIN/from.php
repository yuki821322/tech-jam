<!-- http://localhost:8888/tech-jam/PHPMAIN/from.php -->
<?php
session_start();
$csv_name = $_SESSION['csv_name'];
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../CSS/from/from.css">
    <link rel="stylesheet" href="../CSS/from/header.css">
    <title>ようこそ</title>
</head>

<body>

    <?php include '../PHPMAIN/header.php'; ?>

    <!-- メインエリア -->
    <div class="all">
        <div class="task">
            <h1>タスク管理</h1>
        </div>
        <div class="progress">
            <h1>プログレスバー</h1>
            <progress id="file" max="100" value="70">70%</progress>
        </div>
    </div>
    <script src="../JS/header.js"></script>

</body>

</html>