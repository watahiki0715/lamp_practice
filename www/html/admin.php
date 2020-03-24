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

$token=get_csrf_token();

//データベースに接続
$db = get_db_connect();
//ログインしているユーザーのレコードを取得
$user = get_login_user($db);

//ユーザーがadminでないならLOGINにリダイレクト
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

//全ての商品のレコードを取得
$items = get_all_items($db);
//admin_view.phpの読み込み
include_once VIEW_PATH . '/admin_view.php';
