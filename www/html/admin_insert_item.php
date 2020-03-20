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

//データベースに接続
$db = get_db_connect();
//ログインしているユーザーのレコードを取得
$user = get_login_user($db);

//ユーザーがadminでないならLOGINにリダイレクト
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

//functions.phpのget_post関数でPOSTされたデータを取得
$name = get_post('name');
$price = get_post('price');
$status = get_post('status');
$stock = get_post('stock');

//アップロードされたファイルの配列を取得
$image = get_file('image');

//商品を登録しメッセージを追加
//失敗したらエラーを追加
if(regist_item($db, $name, $price, $stock, $status, $image)){
  set_message('商品を登録しました。');
}else {
  set_error('商品の登録に失敗しました。');
}


//ADMINにリダイレクト
redirect_to(ADMIN_URL);