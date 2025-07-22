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
        <h1>tech-jam</h1>
    </header>
    <h1>ようこそ、<?php echo htmlspecialchars($csv_name); ?> さん！</h1>
    <div class="all">
        <div class="sidebar">
            <ul>
                <li>プロフィール</li>
                <li>ラベル</li>
                <li>マイタスク</li>
                <li>カレンダー</li>
                <li>削除</li>
                <li>会員詳細</li>
                <li>お問合せ</li>
            </ul>
        </div>
        <div class="task">
            <h1>タスク管理</h1>
        </div>
        <div class="progress">プログレスバー</div>
    </div>
</body>

</html>