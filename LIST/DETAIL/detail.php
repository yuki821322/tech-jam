<?php
$filename = '../../CSV/user_data.csv';

// idをURLから取得
$id = isset($_GET['id']) ? $_GET['id'] : '';

if ($id === '') {
    echo 'IDが指定されていません。';
    exit;
}

// ファイル存在チェック
if (!file_exists($filename)) {
    echo 'ファイルが存在しません。';
    exit;
}

// 該当メンバーを探す
$member = null;

if (($fp = fopen($filename, 'r')) !== false) {
    flock($fp, LOCK_SH);
    $header = fgetcsv($fp); // ヘッダー行をスキップ
    while ($row = fgetcsv($fp)) {
        if ($row[0] == $id) {
            $member = $row;
            break; // 見つかったら終了
        }
    }
    flock($fp, LOCK_UN);
    fclose($fp);
}

// メンバーが見つからなかった場合
if ($member === null) {
    echo '該当する会員が見つかりませんでした。';
    exit;
}

// 性別リスト（仮に定義）
$gender_list = [
    0 => '未設定',
    1 => '男性',
    2 => '女性',
    9 => 'その他'
];
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../CSS/list/detail.css">
    <title>会員詳細</title>
</head>

<body>
    <div class="box">
        <h1>詳細</h1>
        <div class="member-block">
            <p><label>ID：</label><span><?php echo htmlspecialchars($member[0], ENT_QUOTES, 'UTF-8'); ?></span></p>
            <p><label>名前：</label><span><?php echo htmlspecialchars($member[1], ENT_QUOTES, 'UTF-8'); ?></span></p>
            <p><label>メール：</label><span><?php echo htmlspecialchars($member[2], ENT_QUOTES, 'UTF-8'); ?></span></p>
            <p><label>生年月日：</label><span><?php echo htmlspecialchars($member[3], ENT_QUOTES, 'UTF-8'); ?></span></p>
            <p><label>性別：</label><span>
                    <?php
                    $gender_code = (int)$member[4];
                    echo isset($gender_list[$gender_code]) ? $gender_list[$gender_code] : '不明';
                    ?>
                </span></p>
            <p><label>ユーザー名：</label><span><?php echo htmlspecialchars($member[5], ENT_QUOTES, 'UTF-8'); ?></span></p>
            <p><label>登録日：</label><span><?php echo htmlspecialchars($member[6], ENT_QUOTES, 'UTF-8'); ?></span></p>
        </div>
    </div>
</body>

</html>