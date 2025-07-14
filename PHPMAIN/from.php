<?php
session_start();
$csv_name = $_SESSION['csv_name'];
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ようこそ</title>
</head>

<body>
    <h1>ようこそ、<?php echo htmlspecialchars($csv_name); ?> さん！</h1>
</body>

</html>