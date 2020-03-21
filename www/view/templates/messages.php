<!--エラーの配列を取得して全て表示-->
<?php foreach(get_errors() as $error){ ?>
  <p class="alert alert-danger"><span><?php print(h($error)); ?></span></p>
<?php } ?>
<!--メッセージの配列を取得して全て表示-->
<?php foreach(get_messages() as $message){ ?>
  <p class="alert alert-success"><span><?php print(h($message)); ?></span></p>
<?php } ?>