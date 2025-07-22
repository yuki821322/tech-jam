<?php
$filename = '../../CSV/user_data.csv';

// GET か POST から ID を取得
$id = $_GET['id'] ?? ($_POST['id'] ?? '');
$members = [];

// CSV 読み込み
if (($fp = fopen($filename, 'r')) !== false) {
    flock($fp, LOCK_SH);
    $header = fgetcsv($fp); // ヘッダー読み飛ばし
    while ($row = fgetcsv($fp)) {
        $members[] = $row;
    }
    flock($fp, LOCK_UN);
    fclose($fp);
}

// 該当メンバーを探す
$target = null;
if ($id !== '') {
    foreach ($members as $row) {
        if ($row[0] === $id) {
            $target = $row;
            break;
        }
    }
}

// POST処理（保存）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($members as &$row) {
        if ($row[0] === $_POST['id']) {
            $row[1] = $_POST['name'];
            $row[2] = $_POST['email'];

            // 生年月日をフォーマットして保存
            $birth_input = $_POST['birth'];
            $birth_date = DateTime::createFromFormat('Y-m-d', $birth_input);
            if ($birth_date) {
                $row[3] = $birth_date->format('Y年m月d日');
            } else {
                $row[3] = '';
            }

            $row[4] = $_POST['gender'];
            $row[5] = $_POST['password'];
            break;
        }
    }
    unset($row);

    if (($fp = fopen($filename, 'w')) !== false) {
        flock($fp, LOCK_EX);
        fputcsv($fp, ['ID', '名前', 'メールアドレス', '生年月日', '性別', 'パスワード', '登録日']);
        foreach ($members as $row) {
            fputcsv($fp, $row);
        }
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    header('Location: ../member_list.php');
    exit;
}

// 📌 編集画面で生年月日を Y-m-d に変換して表示用にセット
$birth_display = '';
if ($target && !empty($target[3])) {
    $birth_date = DateTime::createFromFormat('Y年m月d日', $target[3]);
    if ($birth_date) {
        $birth_display = $birth_date->format('Y-m-d');
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../../CSS/list/edit.css">
    <title>編集画面</title>
</head>

<body>
    <div class="box">
        <h1>会員情報の編集</h1>
        <?php if ($target): ?>
            <form method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($target[0]); ?>">

                <p><span>名前</span><input type="text" name="name" value="<?php echo htmlspecialchars($target[1]); ?>"></p>
                <p><span>メール</span><input type="email" name="email" value="<?php echo htmlspecialchars($target[2]); ?>"></p>
                <p><span>生年月日</span><input type="date" name="birth" value="<?php echo htmlspecialchars($birth_display); ?>"></p>

                <?php
                $gender_list = [
                    0 => '未設定',
                    1 => '男性',
                    2 => '女性',
                    9 => 'その他'
                ];
                ?>
                <p><span>性別</span>
                    <select name="gender">
                        <?php foreach ($gender_list as $key => $value): ?>
                            <option value="<?php echo $key; ?>" <?php if ($target[4] == $key) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($value); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <p><span>パスワード</span><input type="text" name="password" value="<?php echo htmlspecialchars($target[5]); ?>"></p>

                <p><button type="submit">更新</button></p>
            </form>
        <?php else: ?>
            <p>指定されたIDは見つかりませんでした。</p>
        <?php endif; ?>
    </div>
</body>

</html>