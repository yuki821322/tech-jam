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

<body>
    <h1>ようこそ、<?php echo htmlspecialchars($csv_name); ?> さん！</h1>
    <div class="sidebar">サイドバー</div>
    <div class="all">
        <div class="task">タスク管理画面</div>
        <div class="progress">プログレスバー</div>
    </div>
</body>

</html>