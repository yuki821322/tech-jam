<?php
session_start();
$csv_name = $_SESSION['csv_name'];
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../CSS/from/from.css">
    <title>ようこそ</title>
</head>

<!-- http://localhost:8888/tech-jam/PHPMAIN/from.php -->

<body>
    <header>
        <!-- ハンバーガーメニュー -->
        <div class="hamburger" id="hamburger">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>

        <!-- サイドバー -->
        <div class="sidebar" id="sidebar">
            <ul>
                <li><a href="../PHPMAIN/from.php">ホーム</a></li>
                <li><a href="../PHPMAIN/SIDEBAR/profile.php">プロフィール</a></li>
                <li><a href="../PHPMAIN/SIDEBAR/add-task.php">タスク追加</a></li>
                <li><a href="../PHPMAIN/SIDEBAR/mytask.php">マイタスク</a></li>
                <li><a href="../PHPMAIN/SIDEBAR/calendar.php">カレンダー</a></li>
                <li><a href="../LIST/member_list.php">会員詳細</a></li>
                <li>お問合せ</li>
                <li>ログアウト</li>
            </ul>
        </div>
        <div class="overlay" id="overlay"></div>
        <h1>tech-jam</h1>
    </header>

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

    <script src="../JS/from.js"></script>
</body>

</html>