<?php
  require "./vendor/autoload.php"; //ดึงการใช้งานพวก line mpdf
  require_once 'bot_settings.php'; //ดึงการใช้งาน ค่าห้อง id secret

  //ดึงโทเค็นมาใช้
  $httpClient = new CurlHTTPClient(LINE_MESSAGE_ACCESS_TOKEN);
  echo $httpClient;
 ?>
