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
$page = get_get('page');
if($page === ''){
  $page = 1;
}

//公開中の商品数
$items_count = count($items);

//商品一覧のページ数
$page_count = (int)ceil($items_count / 8);

//表示する商品のレコードを取得
$items_page = get_items_page($db, $page);

//件数
//1ページ目以外の場合(最後のページ以外)
if((int)$page !== 1 && $page_count !== (int)$page){
  $item_first = ($page - 1) * 8 + 1;
  $item_last = $item_first + 7;
//1ページ目の場合(最後のページ以外)
}else if((int)$page === 1 && $page_count !== (int)$page){
  $item_first = 1; 
  $item_last = 8; 
//1ページ目以外が最後のページの場合
}else if((int)$page !== 1 && $page_count === (int)$page){
  $item_first = ($page - 1) * 8 + 1;
  $item_last = $items_count;
//1ページ目が最後のページの場合
}else if((int)$page === 1 && $page_count === (int)$page){
  $item_first = 1; 
  $item_last = $items_count;
}

//index_view.phpの読み込み
include_once VIEW_PATH . 'index_view.php';