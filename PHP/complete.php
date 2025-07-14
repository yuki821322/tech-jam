<?php
date_default_timezone_set('Asia/Tokyo');

$registration_data = date('Y-m-d');

$file_name = '../CSV/user_data.csv';

$fp = fopen($file_name, 'r');
$cnt = 0;
while (fgetcsv($fp) !== false) {
    $cnt++;
}
fclose($fp);

$id = $cnt;

$user_name = $_POST['username'];
$email = $_POST['email'];
$birthday = $_POST['birthday'];
$gender = $_POST['gender'];
$password = $_POST['password'];


$record = [
    $id,
    $user_name,
    $email,
    $birthday,
    $gender,
    $password,
    $registration_data
];

$fp = fopen($file_name, 'a');
if (flock($fp, LOCK_EX)) {
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
    <title>登録完了画面</title>
</head>

<body>
    <h1>登録が完了しました。</h1>
    <input type="button" value="トップページへ" onclick="location.href='index.php'">
</body>

</html>