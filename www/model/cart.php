<?php 
//ファイル読み込み
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

//user_idが一致したcartテーブルのレコード全て取得
//失敗したらfalseを返す
function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = :user_id
  ";
  $params = array(':user_id' => $user_id);
  return fetch_all_query($db, $sql, $params);
}

//user_idとitem_idが一致したcartテーブルのレコードを一つ取得
//失敗したらfalseを返す
function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = :user_id
    AND
      items.item_id = :item_id
  ";
  $params = array(':user_id' => $user_id, ':item_id' => $item_id);
  return fetch_query($db, $sql, $params);

}

//get_user_cart関数で取得できなかったらinsert_cart関数で商品をカートに追加
//取得できたらupdate_cart_amount関数でカートの商品のamountに1追加
function add_cart($db, $user_id, $item_id ) {
  $cart = get_user_cart($db, $user_id, $item_id);
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

//カートに商品を登録
function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(':item_id', ':user_id', ':amount')
  ";
  $params = array(':item_id' => $item_id, ':user_id' => $user_id, ':amount' => $amount);
  return execute_query($db, $sql, $params);
}

//カートの商品のamountを更新
function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = {$amount}
    WHERE
      cart_id = {$cart_id}
    LIMIT 1
  ";
  return execute_query($db, $sql);
}

//カート商品を削除
function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = {$cart_id}
    LIMIT 1
  ";

  return execute_query($db, $sql);
}

//validate_cart_purchase関数がfalseならfalseを返して終了
//trueなら商品の在庫から購入する商品の数量を引いて在庫を更新
//更新失敗ならエラーを追加
//繰り返し処理終了後に指定されたユーザーのカートのレコードを削除
function purchase_carts($db, $carts){
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  foreach($carts as $cart){
    if(update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
      ) === false){
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }
  
  delete_user_carts($db, $carts[0]['user_id']);
}

//指定されたユーザーidのカートのレコードを削除
function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = {$user_id}
  ";

  execute_query($db, $sql);
}


//カートの商品の合計金額を取得
function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

//カートに商品がなかったらエラーを追加してfalseを返して終了
//商品があったら表示
//商品が非公開ならエラーを追加
//在庫が足りなければエラーを追加
//functions.phpのhas_error関数でエラーが入っていたらfalseを返して終了
//エラーがなかったらtrueを返して終了
function validate_cart_purchase($carts){
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  foreach($carts as $cart){
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  if(has_error() === true){
    return false;
  }
  return true;
}

