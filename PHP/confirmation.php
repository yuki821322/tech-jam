<!-- <?php
        var_dump($_POST);

        $user_name = $_POST['username'];
        $email = $_POST['email'];
        $birthday = $_POST['birthday'];
        $gender = $_POST['gender']; // Assuming $gendar_list is defined somewhere in your code
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        ?> -->

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/confirmation.css">
    <title>確認・登録画面</title>
</head>

<body>
    <div class="container">
        <h1>確認画面</h1>
        <table>
            <tr>
                <th>名前</th>
                <td><?php echo htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td><?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <th>生年月日</th>
                <td><?php echo htmlspecialchars($birthday, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>

            <?php
            $gendar_list = [
                1 => '男性',
                2 => '女性',
                3 => '未回答',
            ];
            ?>
            <tr>
                <th>性別</th>
                <td><?php echo htmlspecialchars($gendar_list[$gender], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <th>パスワード</th>
                <td><?php
                    for ($i = 0; $i < mb_strlen($password); $i++) {
                        echo '*';
                    }
                    ?>
                </td>
            </tr>
        </table>
        <form action="complete.php" method="post">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="birthday" value="<?php echo htmlspecialchars($birthday, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="gender" value="<?php echo htmlspecialchars($gender, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="password" value="<?php echo htmlspecialchars($password, ENT_QUOTES, 'UTF-8'); ?>">
            <p>
                <input class="submit-button" type="submit" value="登録">
            </p>
        </form>
    </div>
</body>

</html>