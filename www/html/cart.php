<?php
//ファイルを読み込み
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

$token=get_csrf_token();

//データベースに接続
$db = get_db_connect();
//ログインしているユーザーのレコードを取得
$user = get_login_user($db);

//cart.phpのget_user_carts関数でユーザーidが一致した商品のレコードを全て取得
$carts = get_user_carts($db, $user['user_id']);

//商品の合計金額を取得
$total_price = sum_carts($carts);

//cart_view.phpの読み込み
include_once VIEW_PATH . 'cart_view.php';