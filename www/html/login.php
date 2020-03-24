<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';

//セッションを開始する
session_start();

//functions.php  ユーザーIDが空でなかったら「HOME_URL」にリダイレクト
if(is_logined() === true){
  redirect_to(HOME_URL);
}

$token=get_csrf_token();

//login_view.phpの読み込み
include_once VIEW_PATH . 'login_view.php';