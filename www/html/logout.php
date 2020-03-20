<?php
//ファイル読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';

//セッションを開始する
session_start();
//セッション変数を全て削除
$_SESSION = array();
//セッションに関連する設定を取得
$params = session_get_cookie_params();
//セッションに利用しているクッキーの有効期限を過去に設定することで無効化
setcookie(session_name(), '', time() - 42000,
  $params["path"], 
  $params["domain"],
  $params["secure"], 
  $params["httponly"]
);
//セッションIDを無効化
session_destroy();

// ログアウトの処理が完了したらLOGINへリダイレクト
redirect_to(LOGIN_URL);

