<?php
//ファイルの読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

//セッション開始
session_start();

//functions.php  ユーザーIDが空だったらLOGINにリダイレクト
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

//CSRFの対策
$token = get_post('csrf_token');
if(is_valid_csrf_token($token)===false){
  redirect_to(HOME_URL);
}
unset($_SESSION['csrf_token']);

//データベースに接続
$db = get_db_connect();
//ログインしているユーザーのレコードを取得
$user = get_login_user($db);

//functions.phpのget_postでPOSTされたcart_idを取得
$cart_id = get_post('cart_id');

//指定されたcart_idのレコードを削除しメッセージを追加
//失敗したらエラーを追加
if(delete_cart($db, $cart_id)){
  set_message('カートを削除しました。');
} else {
  set_error('カートの削除に失敗しました。');
}

//CARTにリダイレクト
redirect_to(CART_URL);