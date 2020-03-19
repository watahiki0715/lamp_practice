<!DOCTYPE html>
<html lang="ja">
<head>
  <!--head.php(css,scriptなど)の読み込み-->
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>サインアップ</title>
  <!--cssファイルの読み込み-->
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'signup.css'); ?>">
</head>
<body>
  <!--phpファイル(header部)の読み込み-->
  <?php include VIEW_PATH . 'templates/header.php'; ?>
  <div class="container">
    <h1>ユーザー登録</h1>

    <!--phpファイル(message部)の読み込み-->
    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <!--登録が押されたら入力された値を「signup_process.php"」にPOST-->
    <form method="post" action="signup_process.php" class="signup_form mx-auto">
      <div class="form-group">
        <label for="name">名前: </label>
        <input type="text" name="name" id="name" class="form-control">
      </div>
      <div class="form-group">
        <label for="password">パスワード: </label>
        <input type="password" name="password" id="password" class="form-control">
      </div>
      <div class="form-group">
        <label for="password_confirmation">パスワード（確認用）: </label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
      </div>
      <input type="submit" value="登録" class="btn btn-primary">
    </form>
  </div>
</body>
</html>