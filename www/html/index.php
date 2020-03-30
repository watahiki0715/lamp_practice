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

//ページ数の取得(初期値1)
$page = 1;
if(get_get('page') !== ''){
  $page = (int)get_get('page');
}

//公開中の商品数
$items_count = (int)count($items);

//商品一覧のページ数
$page_count = (int)ceil($items_count / DISPLAY_ITEMS);

//表示する商品のレコードを取得
$items_page = get_items_page($db, $page);

//件数
//1ページ目以外の時
if(1 < $page){
  $item_first = ($page - 1) * DISPLAY_ITEMS + 1;
}else{
  //1ページ目の時
  $item_first = 1;
}
//1ページに指定の商品数(8件)が表示される時
if(count($items_page) % DISPLAY_ITEMS === 0){
  $item_last = $item_first + DISPLAY_ITEMS - 1;
}else{
  //1ページに指定の商品数(8件)未満が表示される時
  $item_last = $item_first + $items_count % DISPLAY_ITEMS - 1;
}

//index_view.phpの読み込み
include_once VIEW_PATH . 'index_view.php';