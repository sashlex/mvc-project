<?php 
# main variable $content
?>
<!DOCTYPE html>
<html>
  <head>
    <title>title</title>
    <meta charset="UTF-8">
    <link rel='stylesheet' type='text/css' href='css/style.css'>
    <link rel='stylesheet' type='text/css' href='css/test.css'>
  </head>
  <body>
    <h3>Default layout</h3>
    <?php echo $content; ?>
  </body>
  <div style='font:.7rem/1rem arial;padding:.5rem;width:100%;overfolw:hidden;position:fixed;left:0;bottom:0;background:rgba(0,0,0,0.3);'>
    <b>Information: </b>&nbsp;
    memory: <b><u><?= number_format(memory_get_usage()) ?></u></b>&nbsp;
    time: <b><u><?=  round( microtime( true ) - $_SERVER[ 'REQUEST_TIME_FLOAT' ], 7 ) ?></u></b>
  </div>
</html>
