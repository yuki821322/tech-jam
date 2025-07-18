<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS//index/sign-up.css">
    <title>新規登録</title>
</head>

<body>
    <section class="sign-all">
        <div class="sign">
            <h1>新規登録入力画面</h1>
            <form action="confirmation.php" method="post">
                <p>
                    <input class="input" type="text" id="username" name="username" required placeholder="name">
                </p>
                <p>
                    <input class="input" type="email" id="email" name="email" required placeholder="email">
                </p>
                <p>
                    <input class="input" type="date" id="birthday" name="birthday" required placeholder="YYYY/MM/DD">
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
                <p>
                    <?php foreach ($gendar_list as $key => $value): ?>
                        <input type="radio" name="gender" id="gendar<?php echo $key; ?>"
                            value="<?php echo $key; ?>" onclick="hyouji()">
                        <label for="gendar<?php echo $key; ?>"><?php echo $value; ?></label>
                    <?php endforeach; ?>
                </p>
                </p>
                <p>
                    <input class="input" type="password" id="password" name="password" required placeholder="Password">
                    <br>
                    <span>※パスワードは8文字以上で入力してください</span>
                </p>
                <p>
                    <input class="input" type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm">
                    <br>
                    <span>※パスワードが一致することを確認してください</span>
                </p>
                <p>
                    <a href="confirmation.php"><input class="submit-button" type="submit" value="確認画面へ"></a>
                </p>
            </form>
        </div>
    </section>
    <script src="../JS/sing-up.js"></script>
</body>

</html>