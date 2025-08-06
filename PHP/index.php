<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="../CSS/index/index.css">


  <title>tech-jam</title>
</head>


<!-- http://localhost:8888/tech-jam/php/index.php -->

<body>
  <header>
    <div class="top"></div>
    <h2 class="title">Team Note</h2>
  </header>
  

  
  <div class="blue-line"></div>
  <div class="main-layout">
    <div class="main-container">
    <h1 class="main-title">Team 
      <span class="note">Note.</span>
 </h1>
 </div>
    <div class="container">
      
      <b class="subtext">さぁ<br>Team Noteを始めよう</b>
      <p class="explanation">「Team Note」<br>小規模なチームで利用することに適しているチームタスク管理アプリです。</p>

      <div class="login">
        <button id="open-login"class="button-30" class="btn">ログイン</button>

        <div class="sign-up-btn">
          <a href="sign-up.php" class="button-51" class="sign-up">新規登録</a>
        </div>
      </div>
    </div>
  </div>
  
  <div id="login-modal" class="modal">
    <div class="box">
      <h1>ログインページ</h1>
      <span class="close-btn">&times;</span>

      <!-- エラーがあれば表示 -->
      <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
        <p style="color:red;">メールアドレスまたはパスワードが間違っています。</p>
      <?php endif; ?>

      <form action="login_check.php" method="post">
        <input type="email" name="email" required placeholder="Email"><br>
        <input type="password" name="password" required placeholder="Password"><br>
        <input type="submit" value="ログイン">
      </form>
    </div>
  </div>
  <br>
  <br>
  <br>
  <br>
  <div class="hr1"></div>
  <div class="hr2"></div>
  <div class="hr3"></div>

  <script src="../JS/index.js?v=3.0"></script>
</body>

</html>