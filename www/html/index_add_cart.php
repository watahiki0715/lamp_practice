<?php
//ファイルを読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

//セッションを開始する
session_start();

//functions.php  ユーザーIDが空だったらLOGINにリダイレクト
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

//データベースに接続
$db = get_db_connect();
//ログインしているユーザーのレコードを取得
$user = get_login_user($db);


//商品idが一致した商品のレコードを取得
$item_id = get_post('item_id');

//cart.phpのadd_cartで商品をカートに追加しメッセージを追加
//更新に失敗したらエラーを追加
if(add_cart($db,$user['user_id'], $item_id)){
  set_message('カートに商品を追加しました。');
} else {
  set_error('カートの更新に失敗しました。');
}

//HOMEにリダイレクト
redirect_to(HOME_URL);