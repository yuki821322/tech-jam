<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録</title>
</head>

<body>
    <h1>入力画面</h1>
    <form action="confirmation.php" method="post">
        <p>
            <label for="username">名前:</label>
            <input type="text" id="username" name="username" required>
        </p>
        <p>
            <label for="email">メールアドレス:</label>
            <input type="email" id="email" name="email" required>
        </p>
        <p>
            <label for="birthday">生年月日:</label>
            <input type="date" id="birthday" name="birthday" required>
        </p>
        <?php
        // 性別の項目を管理する連想配列を作る
        $gendar_list = [
            1 => '男性',
            2 => '女性',
            3 => '未回答',
        ];
        ?>
        <p>
            性別<br>
            <?php foreach ($gendar_list as $key => $value): ?>
                <input type="radio" name="gender" id="gendar<?php echo $key; ?>"
                    value="<?php echo $key; ?>"
                    <?php if ($key ===  1)
                        echo 'checked'
                    ?>>
                <label
                    for="gendar<?php echo $key; ?>">
                    <?php echo $value; ?>
                </label>
            <?php endforeach; ?>
        </p>
        <p>
            <label for="password">パスワード:</label>
            <input type="password" id="password" name="password" required>
            <span>※パスワードは8文字以上で入力してください</span>
        </p>
        <p>
            <label for="confirm_password">パスワード確認:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <span>※パスワードが一致することを確認してください</span>
        </p>
        <p>
            <a href="confirmation.php"><input type="submit" value="確認画面へ"></a>
        </p>
    </form>
</body>

</html>