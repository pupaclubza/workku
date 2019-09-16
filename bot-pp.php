<?php
  require "./vendor/autoload.php"; //ดึงการใช้งานพวก line mpdf
  require_once 'bot_settings.php'; //ดึงการใช้งาน ค่าห้อง id secret



  //ดึง class จากพวก line
  use LINE\LINEBot;
  use LINE\LINEBot\HTTPClient;
  use LINE\LINEBot\HTTPClient\CurlHTTPClient;
  use LINE\LINEBot\MessageBuilder;
  use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

  //ดึงโทเค็นมาใช้
  $httpClient = new CurlHTTPClient(LINE_MESSAGE_ACCESS_TOKEN);
  //คือสร้าง array เก็บเลขห้อง
  $bot = new LINEBot($httpClient, array('channelSecret' => LINE_MESSAGE_CHANNEL_SECRET));

  // คำสั่งรอรับการส่งค่ามาของ LINE Messaging API
  $content = file_get_contents('php://input');

  // แปลงข้อความรูปแบบ JSON  ให้อยู่ในโครงสร้างตัวแปร array
  $events = json_decode($content, true);

    if(!is_null($events)){
        $replyToken  = $events['events'][0]['replyToken'];
        $typeMessage = $events['events'][0]['message']['type'];
        $userMessage = $events['events'][0]['message']['text'];

        switch ($typeMessage){
                case 'text':
                            switch ($userMessage){
                                case "id":
                                          $replyData = new TextMessageBuilder("Your ID: ".$events['events'][0]['source']['userId']);
                                          break;
                            }
                            break;
        }
    }
    echo "line OK if ok ok";

    $response = $bot->replyMessage($replyToken,$replyData);
    if ($response->isSucceeded()) {
        echo 'Succeeded!';
        return;
    }




 ?>
