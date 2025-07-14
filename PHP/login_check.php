<?php
session_start();

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$filename = '../CSV/user_data.csv';

if (($fp = fopen($filename, 'r')) !== false) {
    while (($row = fgetcsv($fp)) !== false) {
        $csv_name = trim($row[1]);
        $csv_email = trim($row[2]);
        $csv_password = trim($row[5]);

        if ($email === $csv_email && $password === $csv_password) {
            fclose($fp);
            $_SESSION['csv_name'] = $csv_name;
            header('Location: ../PHPMAIN/from.php');
            exit;
        }
    }
    fclose($fp);
}

echo "ログイン失敗：メールアドレスまたはパスワードが間違っています。";
