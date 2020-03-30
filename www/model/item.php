<?php
//定数ファイルを読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

// DB利用

//db.phpのfetch_query関数で指定された一つのレコードを取得
function get_item($db, $item_id){
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      item_id = :item_id
  ";
  //SQLインジェクション対策
  $params = array(':item_id' => $item_id);
  return fetch_query($db, $sql, $params);
}

//db.phpのfetch_all_query関数で指定された全てのレコードを取得
//$is_openにtrueが入っていたら条件追加して取得
function get_items($db, $is_open = false){
  $sql = '
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
  ';
  if($is_open === true){
    $sql .= '
      WHERE status = 1
    ';
  }

  return fetch_all_query($db, $sql);
}

//全てのレコードを取得
function get_all_items($db){
  return get_items($db);
}

//$is_openがtrueのレコード(公開している)を全て取得
function get_open_items($db){
  return get_items($db, true);
}

//入力された商品情報が適切ならregist_item_transaction関数を実行
//適切でないならfalseを返して終了
function regist_item($db, $name, $price, $stock, $status, $image){
  $filename = get_upload_filename($image);
  if(validate_item($name, $price, $stock, $filename, $status) === false){
    return false;
  }
  return regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename);
}

//商品情報の登録と商品画像の移動が両方成功したら処理を確定しtrueを返す
//どちらか、または両方の処理が失敗したら元の状態に戻してfalseを返す
function regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename){
  $db->beginTransaction();
  if(insert_item($db, $name, $price, $stock, $filename, $status) 
    && save_image($image, $filename)){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
  
}

//商品の登録
function insert_item($db, $name, $price, $stock, $filename, $status){
  $status_value = PERMITTED_ITEM_STATUSES[$status];
  $sql = "
    INSERT INTO
      items(
        name,
        price,
        stock,
        image,
        status
      )
    VALUES(:name, :price, :stock, :filename, :status_value);
  ";
  //SQLインジェクション対策
  $params = array(':name' => $name, ':price' => $price, ':stock' => $stock, ':filename' => $filename, ':status_value' => $status_value);
  return execute_query($db, $sql, $params);
}

//商品のstatusの更新
function update_item_status($db, $item_id, $status){
  $sql = "
    UPDATE
      items
    SET
      status = :status
    WHERE
      item_id = :item_id
    LIMIT 1
  ";
  //SQLインジェクション対策
  $params = array(':status' => $status, ':item_id' => $item_id);
  return execute_query($db, $sql, $params);
}

//商品の在庫の更新
function update_item_stock($db, $item_id, $stock){
  $sql = "
    UPDATE
      items
    SET
      stock = :stock
    WHERE
      item_id = :item_id
    LIMIT 1
  ";
  //SQLインジェクション対策
  $params = array(':stock' => $stock, ':item_id' => $item_id);
  return execute_query($db, $sql, $params);
}

//get_item関数で取得できなかったらfalseを返して終了
//指定された商品のレコードと商品の画像の削除が両方成功したら処理を確定しtrueを返す
//どちらか、または両方の処理が失敗したら元の状態に戻してfalseを返す
function destroy_item($db, $item_id){
  $item = get_item($db, $item_id);
  if($item === false){
    return false;
  }
  $db->beginTransaction();
  if(delete_item($db, $item['item_id'])
    && delete_image($item['image'])){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
}

//指定された商品を削除する
function delete_item($db, $item_id){
  $sql = "
    DELETE FROM
      items
    WHERE
      item_id = :item_id
    LIMIT 1
  ";
  //SQLインジェクション対策
  $params = array(':item_id' => $item_id);
  return execute_query($db, $sql, $params);
}


// 非DB

//statusが1(公開)ならtrueを返す
function is_open($item){
  return $item['status'] === 1;
}

//入力された商品情報が適切か判断し結果を取得
//全ての結果を条件にして返す
function validate_item($name, $price, $stock, $filename, $status){
  $is_valid_item_name = is_valid_item_name($name);
  $is_valid_item_price = is_valid_item_price($price);
  $is_valid_item_stock = is_valid_item_stock($stock);
  $is_valid_item_filename = is_valid_item_filename($filename);
  $is_valid_item_status = is_valid_item_status($status);

  return $is_valid_item_name
    && $is_valid_item_price
    && $is_valid_item_stock
    && $is_valid_item_filename
    && $is_valid_item_status;
}

//入力された商品名の文字数が1以上100以内ならtrue
//違うならエラーを追加しfalseを返す
function is_valid_item_name($name){
  $is_valid = true;
  if(is_valid_length($name, ITEM_NAME_LENGTH_MIN, ITEM_NAME_LENGTH_MAX) === false){
    set_error('商品名は'. ITEM_NAME_LENGTH_MIN . '文字以上、' . ITEM_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  return $is_valid;
}

//入力された商品価格が0以上の整数ならtrue
//違うならエラーを追加しfalseを返す
function is_valid_item_price($price){
  $is_valid = true;
  if(is_positive_integer($price) === false){
    set_error('価格は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

//入力された商品在庫が0以上の整数ならtrue
//違うならエラーを追加しfalseを返す
function is_valid_item_stock($stock){
  $is_valid = true;
  if(is_positive_integer($stock) === false){
    set_error('在庫数は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

//アップロードされたファイルがあればtrue
//なければエラーを追加しfalseを返す
function is_valid_item_filename($filename){
  $is_valid = true;
  if($filename === ''){
    $is_valid = false;
  }
  return $is_valid;
}

//入力された商品statusにopenかcloseが入っていたらtrue
//違うならエラーを追加しfalseを返す
function is_valid_item_status($status){
  $is_valid = true;
  if(isset(PERMITTED_ITEM_STATUSES[$status]) === false){
    $is_valid = false;
  }
  return $is_valid;
}

//ページ数ごとに商品を取得
function get_items_page($db, $page){
  $sql = '
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      status = 1
    LIMIT
  ';

  //ページごとの商品レコードを取得
  $limit = ($page - 1) * DISPLAY_ITEMS;
  $sql .= "$limit,".DISPLAY_ITEMS;

  return fetch_all_query($db, $sql);
}