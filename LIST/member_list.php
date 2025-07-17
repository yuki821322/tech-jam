<?php
$id = $_GET['id'] ?? '';
$filename = '../CSV/user_data.csv';

// ファイル存在チェック
if (!file_exists($filename)) {
    echo 'ファイルが存在しません。';
    exit;
}

// ファイルを開いて読み取り
$fp = fopen($filename, 'r');
$line = null;

// ヘッダー定義（CSVに含まれていない場合）
$header = ['ID', '名前', 'メールアドレス', '登録日', '権限', 'ユーザー名', '最終ログイン日'];

if ($fp && flock($fp, LOCK_EX)) {
    while ($record = fgetcsv($fp)) {
        if ((int)$record[0] === (int)$id) {
            $line = $record;
            break;
        }
    }
    flock($fp, LOCK_UN);
    fclose($fp);
} else {
    echo 'ファイルロックに失敗しました。';
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>会員詳細</title>

    <div class="card">
        <h1>会員詳細</h1>

        <?php if ($line): ?>
            <dl>
                <?php foreach ($header as $key => $label): ?>
                    <dt><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></dt>
                    <dd><?php echo htmlspecialchars($line[$key] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
                <?php endforeach; ?>
            </dl>
        <?php else: ?>
            <p class="not-found">該当する会員が見つかりませんでした。</p>
        <?php endif; ?>
    </div>
    </body>

</html>