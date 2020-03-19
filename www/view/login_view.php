<!DOCTYPE html>
<html lang="ja">
<head>
  <!--head.php(css,scriptなど)の読み込み-->
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>ログイン</title>
  <!--cssファイルの読み込み-->
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'login.css'); ?>">
</head>
<body>
  <!--phpファイル(header部)の読み込み-->
  <?php include VIEW_PATH . 'templates/header.php'; ?>
  <div class="container">
    <h1>ログイン</h1>
    
    <!--phpファイル(message部)の読み込み-->
    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <!--ログインが押されたら「login_process.php」にPOST-->
    <form method="post" action="login_process.php" class="login_form mx-auto">
      <div class="form-group">
        <label for="name">名前: </label>
        <input type="text" name="name" id="name" class="form-control">
      </div>
      <div class="form-group">
        <label for="password">パスワード: </label>
        <input type="password" name="password" id="password" class="form-control">
      </div>
      <input type="submit" value="ログイン" class="btn btn-primary">
    </form>
  </div>
</body>
</html>