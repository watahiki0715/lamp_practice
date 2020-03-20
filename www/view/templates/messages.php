<!--エラーの配列を取得して全て表示-->
<?php foreach(get_errors() as $error){ ?>
  <p class="alert alert-danger"><span><?php print $error; ?></span></p>
<?php } ?>
<!--メッセージの配列を取得して全て表示-->
<?php foreach(get_messages() as $message){ ?>
  <p class="alert alert-success"><span><?php print $message; ?></span></p>
<?php } ?>