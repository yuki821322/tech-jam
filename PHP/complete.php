<?php
date_default_timezone_set('Asia/Tokyo');

$registration_date = date('Y年m月d日');
$file_name = '../CSV/user_data.csv';

// 既存の最大IDを調べる
$max_id = 0;
if (file_exists($file_name)) {
    $fp = fopen($file_name, 'r');
    // ヘッダーがあればスキップ
    $header = fgetcsv($fp);

    while (($row = fgetcsv($fp)) !== false) {
        if (isset($row[0]) && is_numeric($row[0])) {
            $id = (int)$row[0];
            if ($id > $max_id) {
                $max_id = $id;
            }
        }
    }
    fclose($fp);
}
$new_id = $max_id + 1;

$user_name = $_POST['username'];
$email = $_POST['email'];
$birthday_input = $_POST['birthday'];
$birth_date = DateTime::createFromFormat('Y-m-d', $birthday_input);
$birthday = $birth_date ? $birth_date->format('Y年m月d日') : '';
$gender = $_POST['gender'];
$password = $_POST['password'];

$record = [
    $new_id,
    $user_name,
    $email,
    $birthday,
    $gender,
    $password,
    $registration_date
];

/// ファイルサイズを fopen する前にチェックする
$need_header = !file_exists($file_name) || filesize($file_name) === 0;

$fp = fopen($file_name, 'a');
if (flock($fp, LOCK_EX)) {
    if ($need_header) {
        fputcsv($fp, ['id', 'username', 'email', 'birthday', 'gender', 'password', 'registration_date']);
    }
    fputcsv($fp, $record);
    flock($fp, LOCK_UN);
} else {
    echo 'ファイルのロックに失敗しました。';
}
fclose($fp);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/index/compleate.css">
    <title>登録完了画面</title>
</head>


<body>
    <div class="container">
        <h1>登録が完了しました。</h1>
        <input type="button" value="トップページへ" onclick="location.href='index.php'">
    </div>
</body>

</html>