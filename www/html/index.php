<?php
//定数ファイルを読み込み
require_once '../conf/const.php';
//汎用関数ファイルを読み込み
require_once '../model/functions.php';
//userデータに関する関数ファイルを読み込み
require_once '../model/user.php';
//itemデータに関する関数ファイルを読み込み。
require_once '../model/item.php';

//セッションを開始する
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

//公開している商品のレコードを取得
$items = get_open_items($db);

//index_view.phpの読み込み
include_once VIEW_PATH . 'index_view.php';