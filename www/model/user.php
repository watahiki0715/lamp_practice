<?php
//ファイル読み込み
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

//usersテーブルの$user_idと同じuser_idのレコードを取得
function get_user($db, $user_id){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      user_id = {$user_id}
    LIMIT 1
  ";
  //db.phpのfetch_queryでレコードを取得
  return fetch_query($db, $sql);
}

//usersテーブルの$nameと同じnameのレコードを取得
function get_user_by_name($db, $name){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      name = '{$name}'
    LIMIT 1
  ";
  //db.phpのfetch_queryでレコードを取得
  return fetch_query($db, $sql);
}

//get_user_by_name関数でレコードの取得に失敗したかパスワードが違う場合はfalseを返す
//functions.phpのset_session関数で$_SESSION['user_id]に$user['user_id']を入れて$userを返す
//(レコードを取得した配列)
function login_as($db, $name, $password){
  $user = get_user_by_name($db, $name);
  if($user === false || $user['password'] !== $password){
    return false;
  }
  set_session('user_id', $user['user_id']);
  return $user;
}

//functions.phpのget_session関数で$_SESSION['user_id']を取得
//get_user関数で同じuser_idのレコードを取得して返す
function get_login_user($db){
  $login_user_id = get_session('user_id');

  return get_user($db, $login_user_id);
}

//
function regist_user($db, $name, $password, $password_confirmation) {
  if( is_valid_user($name, $password, $password_confirmation) === false){
    return false;
  }
  
  return insert_user($db, $name, $password);
}

//ユーザーtypeがUSER_TYPE_ADMIN(1)の場合trueを返す
//違う場合はfaleを返す
function is_admin($user){
  return $user['type'] === USER_TYPE_ADMIN;
}

//
function is_valid_user($name, $password, $password_confirmation){
  // 短絡評価を避けるため一旦代入。
  $is_valid_user_name = is_valid_user_name($name);
  $is_valid_password = is_valid_password($password, $password_confirmation);
  return $is_valid_user_name && $is_valid_password ;
}

//functions.phpのis_valid_lengthで文字数が6以上100以下がfalseならエラー追加
//パスワードが半角英数字でない場合はエラー追加
//全て当てはまらない場合はtrue,一つでも当てはまる場合はfalseを返す
function is_valid_user_name($name) {
  $is_valid = true;
  if(is_valid_length($name, USER_NAME_LENGTH_MIN, USER_NAME_LENGTH_MAX) === false){
    set_error('ユーザー名は'. USER_NAME_LENGTH_MIN . '文字以上、' . USER_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  if(is_alphanumeric($name) === false){
    set_error('ユーザー名は半角英数字で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

//functions.phpのis_valid_lengthで文字数が6以上100以下がfalseならエラー追加
//パスワードが半角英数字でない場合はエラー追加
//パスワードと確認用パスワードが一致しない場合はエラー追加
//全て当てはまらない場合はtrue,一つでも当てはまる場合はfalseを返す
function is_valid_password($password, $password_confirmation){
  $is_valid = true;
  if(is_valid_length($password, USER_PASSWORD_LENGTH_MIN, USER_PASSWORD_LENGTH_MAX) === false){
    set_error('パスワードは'. USER_PASSWORD_LENGTH_MIN . '文字以上、' . USER_PASSWORD_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  if(is_alphanumeric($password) === false){
    set_error('パスワードは半角英数字で入力してください。');
    $is_valid = false;
  }
  if($password !== $password_confirmation){
    set_error('パスワードがパスワード(確認用)と一致しません。');
    $is_valid = false;
  }
  return $is_valid;
}

//入力された$nameと$passwordをDBテーブルに登録
function insert_user($db, $name, $password){
  $sql = "
    INSERT INTO
      users(name, password)
    VALUES ('{$name}', '{$password}');
  ";

  //DBテーブルを更新
  return execute_query($db, $sql);
}

