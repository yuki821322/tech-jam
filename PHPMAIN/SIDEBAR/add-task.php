<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/tech-jam/CSS/from/header.css">
    <link rel="stylesheet" href="/tech-jam/CSS/from/SIDEBARCSS/add-task.css">
    <title>タスク追加</title>
</head>

<body>
    <?php include '../header.php'; ?>
    <h1>Task file</h1>

    <form action="" method="">
        <p><input class="title" type="text" id="title" name="title" required placeholder="タイトル"></p>
        <p><input class="deadline" type="date" id="deadline" name="deadline" required placeholder="YYYY/MM/DD"></p>

        <?php
        $format_list = [
            1 => 'マルチ',
            2 => 'シングル',
        ];
        ?>

        <p>
            <?php foreach ($format_list as $key => $value): ?>
                <input type="radio" name="gender" id="format<?php echo $key; ?>"
                    value="<?php echo $key; ?>" onclick="hyouji()">
                <label for="format<?php echo $key; ?>"><?php echo $value; ?></label>
            <?php endforeach; ?>
        </p>

        <p><textarea class="form" name="content" rows="3" cols="20" wrap="hard" required placeholder="内容"></textarea></p>
        <p><input type="submit" value="追加"></p>
    </form>

    <script src="/tech-jam/JS/header.js"></script>
</body>

</html>