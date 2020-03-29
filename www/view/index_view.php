<?php header("X-FRAME-OPTIONS: DENY"); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <!--head.php(css,scriptなど)の読み込み-->
  <?php include VIEW_PATH . 'templates/head.php'; ?>

  
  <title>商品一覧</title>
  <!--cssファイルの読み込み-->
  <link rel="stylesheet" href="<?php print(h(STYLESHEET_PATH . 'index.css')); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  

  <div class="container">
    <h1>商品一覧</h1>
    <!--phpファイルの読み込み-->
    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <div class="card-deck">
      <div class="row">
      <?php foreach($items_page as $item){ ?>
        <div class="col-6 item">
          <div class="card h-100 text-center">
            <div class="card-header">
              <?php print (h($item['name'])); ?>
            </div>
            <figure class="card-body">
              <img class="card-img" src="<?php print(h(IMAGE_PATH . $item['image'])); ?>">
              <figcaption>
                <?php print(h(number_format($item['price']))); ?>円
                <!--ストックが0より大きい場合表示-->
                <?php if($item['stock'] > 0){ ?>
                  <form action="index_add_cart.php" method="post">
                    <input type="submit" value="カートに追加" class="btn btn-primary btn-block">
                    <input type="hidden" name="item_id" value="<?php print(h($item['item_id'])); ?>">
                    <input type="hidden" name="csrf_token" value="<?php print(h($token)); ?>">
                  </form>
                <?php } else { ?>
                  <!--ストックが0より小さい場合表示-->
                  <p class="text-danger">現在売り切れです。</p>
                <?php } ?>
              </figcaption>
            </figure>
          </div>
        </div>
      <?php } ?>
      </div>
    </div>
    <ul class="list-inline">
      <!--1ページ目以外の時に前へを表示-->
      <?php if((int)$page !== 1){
        $previous = $page - 1; ?>
        <li class="list-inline-item">
          <a href="index.php?page=<?php print (h($previous)); ?>">前へ</a>
        </li>
      <?php } ?>
      <!--ページ数のリンクを表示-->
      <?php $i = $page_count;
        while($i >= 1){
          $count = $count + 1;
          if($count !== (int)$page){ ?>
            <li class="list-inline-item">
              <a href="index.php?page=<?php print (h($count)); ?>"><?php print (h($count)); ?></a>
            </li>  
          <?php }else{ ?>
            <!--現在のページ数のリンクの色を変える-->
            <li class="list-inline-item">
              <a class="text-dark" href="index.php?page=<?php print (h($count)); ?>"><?php print (h($count)); ?></a>
            </li>  
          <?php }
          $i = $i - 1; 
      } ?>
      <!--最後のページ以外の時に次へを表示-->
      <?php if((int)$page !== (int)$page_count){
        $next = $page + 1; ?>
        <li class="list-inline-item">
          <a href="index.php?page=<?php print (h($next)); ?>">次へ</a>
        </li>
      <?php } ?>
      <!--商品件数を表示-->
      <li class="list-inline-item">
        <span>
          <?php print (h($items_count)); ?>件中
          <?php print (h($item_first.'-'.$item_last)); ?>件目の商品
        </span>
      </li>
    </ul>
  </div>
  
</body>
</html>