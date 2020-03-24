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

//ユーザーがadminでないならLOGINにリダイレクト
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

//functions.phpのget_post関数でPOSTされたitem_idとchanges_toを取得
$item_id = get_post('item_id');
$changes_to = get_post('changes_to');

//$changes_toがopenなら商品を公開にしメッセージを追加
//$changes_toがcloseなら商品を非公開にしメッセージを追加
//それ以外はエラーを追加
if($changes_to === 'open'){
  update_item_status($db, $item_id, ITEM_STATUS_OPEN);
  set_message('ステータスを変更しました。');
}else if($changes_to === 'close'){
  update_item_status($db, $item_id, ITEM_STATUS_CLOSE);
  set_message('ステータスを変更しました。');
}else {
  set_error('不正なリクエストです。');
}


//ADMINにリダイレクト
redirect_to(ADMIN_URL);