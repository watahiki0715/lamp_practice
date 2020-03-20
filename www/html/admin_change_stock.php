<?php
//ファイルの読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

//セッション開始
session_start();

//functions.php  ユーザーIDが空だったらLOGINにリダイレクト
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

//データベースに接続
$db = get_db_connect();
//ログインしているユーザーのレコードを取得
$user = get_login_user($db);

//ユーザーがadminでないならLOGINにリダイレクト
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

//functions.phpのget_post関数でPOSTされたitem_idとstockを取得
$item_id = get_post('item_id');
$stock = get_post('stock');

//商品の在庫を変更しメッセージを追加
//失敗したらエラーを追加
if(update_item_stock($db, $item_id, $stock)){
  set_message('在庫数を変更しました。');
} else {
  set_error('在庫数の変更に失敗しました。');
}

//ADMINにリダイレクト
redirect_to(ADMIN_URL);