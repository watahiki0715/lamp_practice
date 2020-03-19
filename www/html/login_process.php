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

//POSTされた名前、パスワードを取得
$name = get_post('name');
$password = get_post('password');

//user.php  db.php  データベースに接続する関数を取得
$db = get_db_connect();


//user.php  
$user = login_as($db, $name, $password);
if( $user === false){
  set_error('ログインに失敗しました。');
  redirect_to(LOGIN_URL);
}

set_message('ログインしました。');
if ($user['type'] === USER_TYPE_ADMIN){
  redirect_to(ADMIN_URL);
}
redirect_to(HOME_URL);