<?php
$task_csv = '../../CSV/jointtask.csv';

// GETパラメータからIDを取得 (project_id|task_title の形式)
if (!isset($_GET['id'])) {
    echo "不正なアクセスです。";
    exit;
}

list($project_id, $task_title) = explode('|', $_GET['id'], 2);

// CSV読み込み
$tasks = [];
if (file_exists($task_csv) && ($fp = fopen($task_csv, 'r')) !== false) {
    $header = fgetcsv($fp);
    while (($row = fgetcsv($fp)) !== false) {
        $tasks[] = $row;
    }
    fclose($fp);
} else {
    echo "タスクデータが見つかりません。";
    exit;
}

// 編集対象のタスクを探す
$edit_index = null;
foreach ($tasks as $i => $row) {
    if (count($row) >= 5 && $row[0] === $project_id && $row[1] === $task_title) {
        $edit_index = $i;
        break;
    }
}

if ($edit_index === null) {
    echo "該当タスクが見つかりません。";
    exit;
}

// フォーム送信時の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_title = trim($_POST['title']);
    $new_deadline = trim($_POST['deadline']);
    $new_subtasks = trim($_POST['subtasks']);
    $new_creator = trim($_POST['creator']);

    if ($new_title === '') {
        $error = "タイトルは必須です。";
    } else {
        // サブタスクは改行区切りで配列化し、'|'で結合して保存
        $subtasks_array = array_filter(array_map('trim', explode("\n", $new_subtasks)));
        $subtasks_str = implode('|', $subtasks_array);

        // 更新
        $tasks[$edit_index][1] = $new_title;
        $tasks[$edit_index][2] = $new_deadline;
        $tasks[$edit_index][3] = $subtasks_str;
        $tasks[$edit_index][4] = $new_creator;

        // CSV書き込み
        if (($fp = fopen($task_csv, 'w')) !== false) {
            // ヘッダー書き込み
            fputcsv($fp, $header);
            foreach ($tasks as $row) {
                fputcsv($fp, $row);
            }
            fclose($fp);

            // 編集後は一覧に戻る
            header("Location: jointtask.php");
            exit;
        } else {
            $error = "ファイル書き込みに失敗しました。";
        }
    }
} else {
    // 初期値セット
    $current = $tasks[$edit_index];
    $current_title = $current[1];
    $current_deadline = $current[2];
    $current_subtasks = str_replace('|', "\n", $current[3]);
    $current_creator = $current[4];
}
?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/tech-jam/CSS/from/header.css">
    <title>タスク編集</title>
</head>



<head>
    <meta charset="UTF-8" />
    <title>タスク編集</title>
    <style>
        label {
            display: block;
            margin-top: 10px;
        }

        textarea {
            width: 300px;
            height: 100px;
        }

        input[type="text"],
        input[type="date"] {
            width: 300px;
        }

        .error {
            color: red;
        }

        .button {
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <?php include '../header.php'; ?>
    <h1>タスク編集</h1>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form method="post">
        <label>
            タイトル（必須）<br />
            <input type="text" name="title" value="<?php echo htmlspecialchars($current_title ?? '', ENT_QUOTES, 'UTF-8'); ?>" required />
        </label>

        <label>
            締切日<br />
            <input type="date" name="deadline" value="<?php echo htmlspecialchars($current_deadline ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
        </label>

        <label>
            サブタスク（改行区切り）<br />
            <textarea name="subtasks"><?php echo htmlspecialchars($current_subtasks ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </label>

        <button type="submit" class="button">保存</button>
        <a href="jointtask.php">キャンセル</a>
    </form>
    <script src="/tech-jam/JS/header.js"></script>
</body>

</html>