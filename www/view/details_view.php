<?php header("X-FRAME-OPTIONS: DENY"); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <!--head.php(css,scriptなど)の読み込み-->
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入明細</title>
  <!--cssファイルの読み込み-->
  <link rel="stylesheet" href="<?php print(h(STYLESHEET_PATH . 'admin.css')); ?>">
</head>
<body>
  <!--phpファイルの読み込み-->
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>購入明細</h1>

  <div class="container">
    <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>合計金額</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?php print(h($history_id)); ?></td>
            <td><?php print(h($created)); ?></td>
            <td><?php print(h(number_format($total))); ?>円</td>
          </tr>
        </tbody>
      </table>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>商品名</th>
            <th>価格</th>
            <th>購入数</th>
            <th>小計</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($details as $detail){
            $price = $detail['subtotal']/$detail['amount'];
          ?>
          <tr>
            <td><?php print(h($detail['name'])); ?></td>
            <td><?php print(h(number_format($price))); ?>円</td>
            <td><?php print(h($detail['amount'])); ?>個</td>
            <td><?php print(h(number_format($detail['subtotal']))); ?>円</td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
  </div>
</body>
</html>