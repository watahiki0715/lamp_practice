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
  //SQLインジェクション対策
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
  //SQLインジェクション対策
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
    VALUES(:item_id, :user_id, :amount)
  ";
  //SQLインジェクション対策
  $params = array(':item_id' => $item_id, ':user_id' => $user_id, ':amount' => $amount);
  return execute_query($db, $sql, $params);
}

//カートの商品のamountを更新
function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = :amount
    WHERE
      cart_id = :cart_id
    LIMIT 1
  ";
  //SQLインジェクション対策
  $params = array(':amount' => $amount, ':cart_id' => $cart_id);
  return execute_query($db, $sql, $params);
}

//カート商品を削除
function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = :cart_id
    LIMIT 1
  ";
  //SQLインジェクション対策
  $params = array(':cart_id' => $cart_id);
  return execute_query($db, $sql, $params);
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
      user_id = :user_id
  ";
  //SQLインジェクション対策
  $params = array(':user_id' => $user_id);
  execute_query($db, $sql, $params);
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

//購入履歴に登録
function insert_history($db, $user_id, $total){
  $sql = "
    INSERT INTO
      history(
        user_id,
        total
      )
    VALUES(:user_id, :total)
  ";
  //SQLインジェクション対策
  $params = array(':user_id' => $user_id, ':total' => $total);
  // SQL文を実行する準備
  $statement = $db->prepare($sql);
  // SQLを実行
  return $statement->execute($params);
}

//user_idが一致したhistoryテーブルのレコード全て取得
function get_user_history($db, $user_id){
  $sql = "
    SELECT
      history_id,
      user_id,
      total
    FROM
      history
    WHERE
      user_id = :user_id
    ORDER BY history_id DESC
  ";
  //SQLインジェクション対策
  $params = array(':user_id' => $user_id);
  // SQL文を実行する準備
  $statement = $db->prepare($sql);
  // SQLを実行
  $statement->execute($params);
  // レコードの取得
  $history = $statement->fetch();
  //購入履歴のidを取得
  return $history['history_id'];
}

//購入明細に登録
function insert_details($db, $carts, $history_id){
  foreach($carts as $cart){
    $item_id = $cart['item_id'];
    $amount = $cart['amount'];
    $subtotal = $cart['price'] * $cart['amount'];

    $sql = "
      INSERT INTO
        details(
          history_id,
          item_id,
          amount,
          subtotal
        )
      VALUES(:history_id, :item_id, :amount, :subtotal)
    ";
    //SQLインジェクション対策
    $params = array(':history_id' => $history_id, ':item_id' => $item_id, ':amount' => $amount, ':subtotal' => $subtotal);
    // SQL文を実行する準備
    $statement = $db->prepare($sql);
    // SQLを実行
    $statement->execute($params);
  }
  return;
}

//購入履歴と購入明細を登録
function insert_history_details($db, $user_id, $total, $carts){
  try{
    $db->beginTransaction();
    try{
      //購入履歴に登録
      insert_history($db, $user_id, $total);
      //購入履歴idを取得
      $history_id = get_user_history($db, $user_id);
      //購入明細に登録
      insert_details($db, $carts, $history_id);
      // コミット処理
      return $db->commit();
    } catch (PDOException $e) {
      // ロールバック処理
      $db->rollback();
      // 例外をスロー
      throw $e;
    }
  } catch (PDOException $e) {
    set_error('データ取得に失敗しました。');
  }
  return false;
}