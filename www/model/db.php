<?php

//データベースに接続する
function get_db_connect(){
  // MySQL用のDSN文字列
  $dsn = 'mysql:dbname='. DB_NAME .';host='. DB_HOST .';charset='.DB_CHARSET;
 
  try {
    // データベースに接続
    //データベースの情報を指定
    $dbh = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    //エラーモードの設定
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //プリペアドステートメントの設定
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    //FetchModeの設定　ASSOC(カラム名の文字列キーのみをキーとした連想配列の配列を返すモード)
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    exit('接続できませんでした。理由：'.$e->getMessage() );
  }
  return $dbh;
}

//DBの一つ目のレコード取得
function fetch_query($db, $sql, $params = array()){
  try{
    // SQL文を実行する準備
    $statement = $db->prepare($sql);
    // SQLを実行
    $statement->execute($params);
    // レコードの取得
    return $statement->fetch();
   //function.phpのset_error関数によりエラーを追加
  }catch(PDOException $e){
    set_error('データ取得に失敗しました。');
  }
  return false;
}

//DBの全てのレコード取得
function fetch_all_query($db, $sql, $params = array()){
  try{
    // SQL文を実行する準備
    $statement = $db->prepare($sql);
    // SQLを実行
    $statement->execute($params);
    // レコードの取得
    return $statement->fetchAll();
   //function.phpのset_error関数によりエラーを追加
  }catch(PDOException $e){
    set_error('データ取得に失敗しました。');
  }
  return false;
}

function execute_query($db, $sql, $params = array()){
  try{
    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }catch(PDOException $e){
    set_error('更新に失敗しました。');
  }
  return false;
}