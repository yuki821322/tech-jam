<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン画面</title>
</head>

<body>
    <h1>ログインページ</h1>
    <form action="login_check.php" method="post">
        <label for="email">メールアドレス:</label>
        <input type="email" name="email" required><br>

        <label for="password">パスワード:</label>
        <input type="password" name="password" required><br>

        <input type="submit" value="ログイン">
    </form>
    <!-- 隠しfromを使って名前をfrom.phpに送る -->
    <form action="from.php" method="post" style="display: none;">
        <input type="text" name="csv_name" value="<?php echo htmlspecialchars($csv_name); ?>">
    </form>
</body>

</html>