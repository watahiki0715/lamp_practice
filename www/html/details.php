<?php
//ファイルの読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'history.php';

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

//購入履歴取得
$history_id = get_post('history_id');
$created = get_post('created');
$total = get_post('total');
$history_user_id = (int)get_post('user_id');

//管理者かユーザーidが一致したら購入明細を取得
if($history_user_id === $user['user_id'] || is_admin($user) === true){
  $details = get_user_details($db, $history_id);
}

//購入詳細の取得確認
if($details === false){
  set_error('購入明細が取得できませんでした。');
  redirect_to(HISTORY_URL);
}

//details_view.phpの読み込み
include_once VIEW_PATH . 'details_view.php';