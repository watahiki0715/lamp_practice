<?php 
//ファイル読み込み
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

//購入履歴の取得(ユーザー)
function get_user_history($db, $user_id){
    $sql = "
      SELECT
        history_id,
        user_id,
        created,
        total
      FROM
        history
      WHERE
        user_id = :user_id
      ORDER BY
        history_id DESC
    ";
    //SQLインジェクション対策
    $params = array(':user_id' => $user_id);
    return fetch_all_query($db, $sql, $params);
}

//購入履歴の取得(管理者)
function get_admin_history($db){
    $sql = "
      SELECT
        history_id,
        user_id,
        created,
        total
      FROM
        history
      ORDER BY
        history_id DESC
    ";
    return fetch_all_query($db, $sql);
}

//購入明細の取得
function get_user_details($db, $history_id){
    $sql = "
      SELECT
        details.history_id,
        details.amount,
        details.subtotal,
        items.item_id,
        items.name
      FROM
        details
      JOIN
        items
      ON
        details.item_id = items.item_id
      WHERE
        details.history_id = :history_id
    ";
    //SQLインジェクション対策
    $params = array(':history_id' => $history_id);
    return fetch_all_query($db, $sql, $params);
}