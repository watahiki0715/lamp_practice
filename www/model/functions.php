<?php

//引数をダンプして終了
function dd($var){
  var_dump($var);
  exit();
}

//引数(url)にリダイレクトして終了
function redirect_to($url){
  header('Location: ' . $url);
  exit;
}

//$_GET配列の引数の値が入っていたらその値を返す
//入っていなかったら''を返す
function get_get($name){
  if(isset($_GET[$name]) === true){
    return $_GET[$name];
  };
  return '';
}

///$_POST配列の引数の値が入っていたらその値を返す
//入っていなかったら''を返す
function get_post($name){
  if(isset($_POST[$name]) === true){
    return $_POST[$name];
  };
  return '';
}

///$_FILES配列の引数の値が入っていたら(ファイルがアップロードされたら)その値を返す
//入っていなかったら空の配列を返す
function get_file($name){
  if(isset($_FILES[$name]) === true){
    return $_FILES[$name];
  };
  return array();
}

//$_SESSION配列の引数の値が入っていたらその値を返す
//入っていなかったら''を返す
function get_session($name){
  if(isset($_SESSION[$name]) === true){
    return $_SESSION[$name];
  };
  return '';
}

//$_SESSION配列の$nameに$valueを入れる
function set_session($name, $value){
  $_SESSION[$name] = $value;
}

//$_SESSION['__errors']配列に引数を追加
function set_error($error){
  $_SESSION['__errors'][] = $error;
}

//get_session関数で$_SESSION['__errors']を取得し$errorsに入れる
//$errorsが''なら空の配列を返す
//set_session関数で$_SESSION['__errors']に空の配列を入れて初期化
//$errorsを返す
function get_errors(){
  $errors = get_session('__errors');
  if($errors === ''){
    return array();
  }
  set_session('__errors',  array());
  return $errors;
}

//$_SESSION['__errors']が入っているかつ$_SESSION['__errors']に入っている値の数が0でなかったらtrueを返す
function has_error(){
  return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}

//$_SESSION['__messages']配列に引数を追加
function set_message($message){
  $_SESSION['__messages'][] = $message;
}

//get_session関数で$_SESSION['__messages']を取得し$messagesに入れる
//$messagesが''の場合空の配列を返す
//set_session関数で$_SESSION['__messages']に空の配列を入れて初期化
//$messagesを返す
function get_messages(){
  $messages = get_session('__messages');
  if($messages === ''){
    return array();
  }
  set_session('__messages',  array());
  return $messages;
}

//get_session関数で$_SESSION['user_id']を取得し$_SESSION['user_id']が''でなかったらtrueを返す
function is_logined(){
  return get_session('user_id') !== '';
}

//is_valid_upload_image関数がfalseなら''を返す
function get_upload_filename($file){
  if(is_valid_upload_image($file) === false){
    return '';
  }
  //ファイルイメージの型(拡張子)を取得
  $mimetype = exif_imagetype($file['tmp_name']);
  $ext = PERMITTED_IMAGE_TYPES[$mimetype];
  //get_random_string関数でデフォルト(20文字)のランダムな文字列を取得
  //拡張子と組み合わせてファイル名を作成して返す
  return get_random_string() . '.' . $ext;
}

//ランダムで文字列を取得
function get_random_string($length = 20){
  //hash関数でユニークな256ビットの文字列を取得し基数を16から36に変換しsubstr関数で0番目から$lengthのバイト分の文字列を返す
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

//アップロードされたファイルを移動させる
function save_image($image, $filename){
  return move_uploaded_file($image['tmp_name'], IMAGE_DIR . $filename);
}

//IMAGE_DIR . $filenameがあったら削除してtrueを返す
//なかったらfalseを返す
function delete_image($filename){
  if(file_exists(IMAGE_DIR . $filename) === true){
    unlink(IMAGE_DIR . $filename);
    return true;
  }
  return false;
  
}



//入力された文字列の文字数を取得
//指定文字数内ならtrue  それ以外はfalseを返す 
function is_valid_length($string, $minimum_length, $maximum_length = PHP_INT_MAX){
  $length = mb_strlen($string);
  return ($minimum_length <= $length) && ($length <= $maximum_length);
}

//引数が半角英数字のみならtrue,違うならfalseを返す
function is_alphanumeric($string){
  return is_valid_format($string, REGEXP_ALPHANUMERIC);
}

//引数が半角数字の整数のみならtrue,違うならfalseを返す
function is_positive_integer($string){
  return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}

//preg_match関数が1(一致)ならtrue,違うならfalseを返す
function is_valid_format($string, $format){
  return preg_match($format, $string) === 1;
}


//is_uploaded_fileがfalse(ファイルがアップロード失敗)ならエラーを追加
function is_valid_upload_image($image){
  if(is_uploaded_file($image['tmp_name']) === false){
    set_error('ファイル形式が不正です。');
    return false;
  }
  //exif_imagetype関数でイメージの型(拡張子)を定義する
  //jpg,png以外ならエラーを追加してfalseを返す
  //jpg,pngならtrueを返す
  $mimetype = exif_imagetype($image['tmp_name']);
  if( isset(PERMITTED_IMAGE_TYPES[$mimetype]) === false ){
    set_error('ファイル形式は' . implode('、', PERMITTED_IMAGE_TYPES) . 'のみ利用可能です。');
    return false;
  }
  return true;
}

//引数をhtmlエスケープ処理する
function h($string){
  return htmlspecialchars($string,ENT_QUOTES,"UTF-8");
}

// トークンの生成
function get_csrf_token(){
  // get_random_string()はユーザー定義関数。
  $token = get_random_string(30);
  // set_session()はユーザー定義関数。
  set_session('csrf_token', $token);
  return $token;
}

// トークンのチェック
function is_valid_csrf_token($token){
  if($token === '') {
    return false;
  }
  // get_session()はユーザー定義関数
  return $token === get_session('csrf_token');
}