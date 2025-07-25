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
        <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <h1 style="margin-left: 20px;">tech-jam</h1>
    </header>
    <div class="all">
        <div class="sidebar hidden" id="sidebar">
            <div class="close-btn" id="close-btn">
                <span></span>
                <span></span>
            </div>
            <ul>
                <li><a href="../PHPMAIN/from.php">ホーム</a></li>
                <li><a href="../PHPMAIN/SIDEBAR/profile.php">プロフィール</a></li>
                <li>ラベル</li>
                <li><a href="../PHPMAIN/SIDEBAR/mytask.php">マイタスク</a></li>
                <li><a href="../PHPMAIN/SIDEBAR/calendar.php">カレンダー</a></li>
                <li>削除</li>
                <li><a href="../LIST/member_list.php">会員詳細</a></li>
                <li>お問合せ</li>
            </ul>
        </div>
        <div class="task">
            <h1>タスク管理</h1>
        </div>
        <div class="progress">
            <h1>プログレンスバー</h1>
            <progress id="file" max="100" value="70">70%</progress>
        </div>
    </div>
    <script src="../JS/from.js"></script>
</body>

</html>