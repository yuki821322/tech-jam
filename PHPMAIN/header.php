<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['csv_name'])) {
    header("Location: /tech-jam/PHP/index.php");
    exit;
}
?>

<header>
    <!-- ハンバーガーメニュー -->
    <div class="hamburger" id="hamburger">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </div>

    <!-- サイドバー -->
    <div class="sidebar" id="sidebar">
        <ul>
            <li><a href="/tech-jam/PHPMAIN/from.php"><img src="/tech-jam/IMG/家のアイコン素材.png" alt="ホーム">Home</a></li>
            <li><a href="/tech-jam/PHPMAIN/SIDEBAR/add-task.php"><img src="/tech-jam/IMG/プラスのアイコン素材.png" alt="追加">Task・Project</a></li>
            <li><a href="/tech-jam/PHPMAIN/SIDEBAR/mytask.php"><img src="/tech-jam/IMG/タスクトレイのフリー素材.png" alt="マイタスク">Mytask ボックス</a></li>
            <li><a href="/tech-jam/PHPMAIN/SIDEBAR/jointtask.php"><img src="/tech-jam/IMG/チーム.png" alt="共同タスク">Teamtask ボックス</a></li>
            <li><a href="/tech-jam/PHPMAIN/SIDEBAR/calendar.php"><img src="/tech-jam/IMG/カレンダーのフリーアイコン2.png" alt="カレンダー">カレンダー</a></li>
            <li><a href="/tech-jam/LIST/member_list.php">会員詳細</a></li>
            <li>お問合せ</li>
            <li><a href="/tech-jam/PHPMAIN/logout.php" onclick="return confirm('本当にログアウトしてもいいですか？');">ログアウト</a></li>
        </ul>
    </div>
    <div class="overlay" id="overlay"></div>
    <h1>tech-jam</h1>
</header>