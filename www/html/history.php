<?php
//ファイルの読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'history.php';

//セッション開始
session_start();

//ユーザーIDが空だったらLOGINにリダイレクト
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

//トークン作成
$token = get_csrf_token();

//データベースに接続
$db = get_db_connect();

//ログインしているユーザーのレコードを取得
$user = get_login_user($db);

//ユーザーidが一致した購入履歴を全て取得
if(is_admin($user) === false){
    $historys = get_user_history($db, $user['user_id']);
}else{
    //管理者用
    $historys = get_admin_history($db);
}

//購入履歴の取得確認
if($historys === false){
    set_error('購入履歴が取得できませんでした。');
    redirect_to(CART_URL);
} 

//history_view.phpの読み込み
include_once VIEW_PATH . 'history_view.php';