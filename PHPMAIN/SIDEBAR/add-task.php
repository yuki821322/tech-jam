<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タスク追加</title>
</head>

<body>
    <h1>Task file</h1>
    <form action="" method="">
        <p><input class="title" type="text" id="title" name="title" repuired placeholder="タイトル"></p>
        <p><input class="deadline" type="date" id="deadline" name="deadline" repuired placeholder="YYYY/MM/DD"></p>
        <?php
        // 性別の項目を管理する連想配列を作る
        $format_list = [
            1 => 'マルチ',
            2 => 'シングル',
        ];
        ?>
        <p>
        <p>
            <?php foreach ($format_list as $key => $value): ?>
                <input type="radio" name="gender" id="fromat<?php echo $key; ?>"
                    value="<?php echo $key; ?>" onclick="hyouji()">
                <label for="gendar<?php echo $key; ?>"><?php echo $value; ?></label>
            <?php endforeach; ?>
        </p>
        <p><textarea class="form" name="content" value="" rows="3" cols="20" wrap="hard" repuired placeholder="内容"></textarea></p>
        <p><input type="submit" value="追加"></p>
    </form>
</body>

</html>