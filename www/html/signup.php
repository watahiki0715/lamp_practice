<?php
//ファイル読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';

//ログインチェックを行うため、セッションを開始する
session_start();

//functions.phpのis_logined関数でログイン済ならHOMEにリダイレクト
if(is_logined() === true){
  redirect_to(HOME_URL);
}

$token=get_csrf_token();

//signup_view.phpの読み込み
include_once VIEW_PATH . 'signup_view.php';



