<?php
//ファイル読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';

//ログインチェックを行うため、セッションを開始する
session_start();

//functions.phpのis_logined関数でログイン済ならHOMEにリダイレクト
if(is_logined() === true){
  redirect_to(HOME_URL);
}

//functions.phpのget_post関数でPOSTされた値を取得
$name = get_post('name');
$password = get_post('password');
$password_confirmation = get_post('password_confirmation');

//db.phpのget_db_connect関数でデータベースに接続
$db = get_db_connect();

try{
  //user.phpのregist_user関数がfalseならエラーを追加してSIGNUPにリダイレクト
  $result = regist_user($db, $name, $password, $password_confirmation);
  if( $result=== false){
    set_error('ユーザー登録に失敗しました。');
    redirect_to(SIGNUP_URL);
  }
}catch(PDOException $e){
  set_error('ユーザー登録に失敗しました。');
  redirect_to(SIGNUP_URL);
}

//user.phpのregist_user関数がtrueならメッセージを追加
set_message('ユーザー登録が完了しました。');
//user.phpのlogin_as関数でログイン
login_as($db, $name, $password);
//HOMEにリダイレクト
redirect_to(HOME_URL);