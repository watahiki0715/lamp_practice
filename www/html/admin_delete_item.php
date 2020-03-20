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

//functions.phpのget_post関数でPOSTされたitem_idを取得
$item_id = get_post('item_id');


//商品を削除しメッセージを追加
//失敗ならエラーを追加
if(destroy_item($db, $item_id) === true){
  set_message('商品を削除しました。');
} else {
  set_error('商品削除に失敗しました。');
}



//ADMINにリダイレクト
redirect_to(ADMIN_URL);