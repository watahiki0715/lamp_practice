<?php header("X-FRAME-OPTIONS: DENY"); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <!--head.php(css,scriptなど)の読み込み-->
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入履歴</title>
  <!--cssファイルの読み込み-->
  <link rel="stylesheet" href="<?php print(h(STYLESHEET_PATH . 'admin.css')); ?>">
</head>
<body>
  <!--phpファイルの読み込み-->
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>購入履歴</h1>
  
  <div class="container">
    <!--phpファイルの読み込み-->
    <?php include VIEW_PATH . 'templates/messages.php'; ?>
      <table class="table table-bordered">
      <?php if(count($historys) > 0){ ?>
        <thead class="thead-light">
          <tr>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>合計金額</th>
            <th>購入明細</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($historys as $history){ ?>
          <tr>
            <td><?php print(h($history['history_id'])); ?></td>
            <td><?php print(h($history['created'])); ?></td>
            <td><?php print(h(number_format($history['total']))); ?>円</td>
            <td>
              <form method="post" action="details.php">
                <input class="btn btn-block btn-primary" type="submit" value="表示する">
                <input type="hidden" name="history_id" value="<?php print(h($history['history_id'])); ?>">
                <input type="hidden" name="created" value="<?php print(h($history['created'])); ?>">
                <input type="hidden" name="total" value="<?php print(h($history['total'])); ?>">
                <input type="hidden" name="user_id" value="<?php print(h($history['user_id'])); ?>">
                <input type="hidden" name="csrf_token" value="<?php print(h($token)); ?>">
              </form>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php } else { ?>
      <p>購入履歴はありません。</p>
    <?php } ?> 
  </div>
</body>
</html>