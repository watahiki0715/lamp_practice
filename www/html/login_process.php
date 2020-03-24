<?php
//ファイル読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';

//セッション開始
session_start();

//functions.php  ユーザーIDが空でなかったら「HOME_URL」にリダイレクト
if(is_logined() === true){
  redirect_to(HOME_URL);
}

//CSRFの対策
$token = get_post('csrf_token');
if(is_valid_csrf_token($token)===false){
  redirect_to(LOGIN_URL);
}
unset($_SESSION['csrf_token']);

//POSTされた名前、パスワードを取得
$name = get_post('name');
$password = get_post('password');

//データベースに接続
$db = get_db_connect();


//user.phpのlogin_as関数でfalseならエラーを追加しLOGINにリダイレクト
$user = login_as($db, $name, $password);
if( $user === false){
  set_error('ログインに失敗しました。');
  redirect_to(LOGIN_URL);
}

//user.phpのlogin_as関数でログイン
//$user['type']がUSER_TYPE_ADMINならADMINにリダイレクト
//それ以外ならHOMEにリダイレクト
set_message('ログインしました。');
if ($user['type'] === USER_TYPE_ADMIN){
  redirect_to(ADMIN_URL);
}
redirect_to(HOME_URL);