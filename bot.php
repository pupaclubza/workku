<?php
// กรณีต้องการตรวจสอบการแจ้ง error ให้เปิด 3 บรรทัดล่างนี้ให้ทำงาน กรณีไม่ ให้ comment ปิดไป
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
// include composer autoload
require_once './vendor/autoload.php';
 
// การตั้งเกี่ยวกับ bot
require_once 'bot_settings.php';
 
// กรณีมีการเชื่อมต่อกับฐานข้อมูล
//require_once("dbconnect.php");
 
///////////// ส่วนของการเรียกใช้งาน class ผ่าน namespace
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
//use LINE\LINEBot\Event;
//use LINE\LINEBot\Event\BaseEvent;
//use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder ;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\DatetimePickerTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;
 
// เชื่อมต่อกับ LINE Messaging API
$httpClient = new CurlHTTPClient(LINE_MESSAGE_ACCESS_TOKEN);
$bot = new LINEBot($httpClient, array('channelSecret' => LINE_MESSAGE_CHANNEL_SECRET));
 
// คำสั่งรอรับการส่งค่ามาของ LINE Messaging API
$content = file_get_contents('php://input');
 
// แปลงข้อความรูปแบบ JSON  ให้อยู่ในโครงสร้างตัวแปร array
$events = json_decode($content, true);

file_put_contents('log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);

if(!is_null($events)){
    // ถ้ามีค่า สร้างตัวแปรเก็บ replyToken ไว้ใช้งาน
    $replyToken  = $events['events'][0]['replyToken'];
	$typeMessage = $events['events'][0]['message']['type'];
    $userMessage = $events['events'][0]['message']['text'];
    $userMessage = strtolower($userMessage);

    switch ($typeMessage){
        case 'text':
            switch ($userMessage) {

            	
                case "id":
                    //$textReplyMessage = "Bot ตอบกลับคุณเป็นข้อความ";
                    //$replyData = new TextMessageBuilder($textReplyMessage);

				    $replyData = new TextMessageBuilder("Your ID: ".$events['events'][0]['source']['userId']);
					//file_put_contents('logid.txt', $events['events'][0]['source']['userId'], FILE_APPEND);
                    break;
                case "c":
					$Command = " Total Menu:\nt   = Text Message\nid  = show Line id\nl   = location\ni   = image\nv   = video\na   = audio\nim  = image map\ntm  = tempate message\ns   = sticker\nm   = multi send\ntb  = button\ntf  = conform\ntc  = Carousel";
				    $replyData = new TextMessageBuilder($Command);
										
					break;
                case "t":
                    $textReplyMessage = "Bot ตอบกลับคุณเป็นข้อความ";
                    $replyData = new TextMessageBuilder($textReplyMessage);  

                    break;
                case "l":
                    $placeName = "ที่ตั้งร้าน";
                    $placeAddress = "แขวง พลับพลา เขต วังทองหลาง กรุงเทพมหานคร ประเทศไทย";
                    $latitude = 13.780401863217657;
                    $longitude = 100.61141967773438;
                    $replyData = new LocationMessageBuilder($placeName, $placeAddress, $latitude ,$longitude);              
                    break;
                case "i":
                    $picFullSize = 'https://www.ninenik.com/imgsrc/photos/f/simpleflower';
                    $picThumbnail = 'https://www.ninenik.com/imgsrc/photos/f/simpleflower/240';
                    $replyData = new ImageMessageBuilder($picFullSize,$picThumbnail);
                    break;
                case "v":
                    $picThumbnail = 'https://www.ninenik.com/imgsrc/photos/f/sampleimage/240';
                    $videoUrl = "https://www.ninenik.com/simplevideo.mp4";                
                    $replyData = new VideoMessageBuilder($videoUrl,$picThumbnail);
                    break;
                case "a":
                    $audioUrl = "https://www.ninenik.com/simpleaudio.mp3";
                    $replyData = new AudioMessageBuilder($audioUrl,27000);
                    break;
				case "im":
                    $imageMapUrl = 'https://www.seagull-brand.com/tssphpbot/linebot/imgsrc/photos/w/sampleimagemap';
					//$imageMapUrl = 'https://www.mywebsite.com/imgsrc/photos/w/sampleimagemap';


                    $replyData = new ImagemapMessageBuilder(
                        $imageMapUrl,
                        'This is Title',
                        new BaseSizeBuilder(699,1040),
                        array(
                            new ImagemapMessageActionBuilder(
                                's',
                                new AreaBuilder(0,0,520,699)
                                ),
                            new ImagemapUriActionBuilder(
                                'https://www.seagull-brand.com',
                                new AreaBuilder(520,0,520,699)
                                )
                             )
						); 
                    break;  
                 case "tm":
                    $replyData = new TemplateMessageBuilder('Confirm Template',
                        new ConfirmTemplateBuilder(
                                'Confirm template builder',
                                array(
                                    new MessageTemplateActionBuilder(
                                        'Yes',
                                        'Text Yes'
                                    ),
                                    new MessageTemplateActionBuilder(
                                        'No',
                                        'Text NO'
                                    )
                                )
                        )
                    );
                    break;     
				case "s":
                    $stickerID = 22;
                    $packageID = 2;
                    $replyData = new StickerMessageBuilder($packageID,$stickerID);
                    break;


                case "m":
					 $arr_replyData = array();
						$textReplyMessage = "Bot ตอบกลับคุณเป็นข้อความ";
						$arr_replyData[] = new TextMessageBuilder($textReplyMessage);
										 
						$stickerID = 22;
						$packageID = 2;
						$arr_replyData[]  = new StickerMessageBuilder($packageID,$stickerID);
										 
						$placeName = "ที่ตั้งร้าน";
						$placeAddress = "แขวง พลับพลา เขต วังทองหลาง กรุงเทพมหานคร ประเทศไทย";
						$latitude = 13.780401863217657;
						$longitude = 100.61141967773438;
						$arr_replyData[] = new LocationMessageBuilder($placeName, $placeAddress, $latitude ,$longitude);        
					 
						$multiMessage =     new MultiMessageBuilder;
						foreach($arr_replyData as $arr_Reply){
								$multiMessage->add($arr_Reply);
						}
						$replyData = $multiMessage;                                     
						break;      
				case "tb":
					// กำหนด action 4 ปุ่ม 4 ประเภท
					$actionBuilder = array(
						new MessageTemplateActionBuilder(
							'Message Template',// ข้อความแสดงในปุ่ม
							's' // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
						),
						new UriTemplateActionBuilder(
							'Uri Template', // ข้อความแสดงในปุ่ม
							'https://www.seagull-brand.com'
						),
						new DatetimePickerTemplateActionBuilder(
							'Datetime Picker', // ข้อความแสดงในปุ่ม
							http_build_query(array(
								'action'=>'reservation',
								'person'=>5
							)), // ข้อมูลที่จะส่งไปใน webhook ผ่าน postback event
							'datetime', // date | time | datetime รูปแบบข้อมูลที่จะส่ง ในที่นี้ใช้ datatime
							substr_replace(date("Y-m-d H:i"),'T',10,1), // วันที่ เวลา ค่าเริ่มต้นที่ถูกเลือก
							substr_replace(date("Y-m-d H:i",strtotime("+5 day")),'T',10,1), //วันที่ เวลา มากสุดที่เลือกได้
							substr_replace(date("Y-m-d H:i"),'T',10,1) //วันที่ เวลา น้อยสุดที่เลือกได้
						),      
						new PostbackTemplateActionBuilder(
							'Postback', // ข้อความแสดงในปุ่ม
							http_build_query(array(
								'action'=>'buy',
								'item'=>100
							)), // ข้อมูลที่จะส่งไปใน webhook ผ่าน postback event
							'Postback Text'  // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
						),      
					);
					$imageUrl = 'https://www.seagull-brand.com/tssphpbot/linebot/imgsrc/photos/w/simpleflower';
					$replyData = new TemplateMessageBuilder('Button Template',
						new ButtonTemplateBuilder(
								'button template builder', // กำหนดหัวเรื่อง
								'Please select', // กำหนดรายละเอียด
								$imageUrl, // กำหนด url รุปภาพ
								$actionBuilder  // กำหนด action object
						)
					);              
					break;  


					case "tf":
					    $replyData = new TemplateMessageBuilder('Confirm Template',
				        new ConfirmTemplateBuilder(
						'Confirm template builder', // ข้อความแนะนำหรือบอกวิธีการ หรือคำอธิบาย
		                array(
				            new MessageTemplateActionBuilder(
						        'Yes', // ข้อความสำหรับปุ่มแรก
								'YES'  // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
		                    ),
							new MessageTemplateActionBuilder(
					            'No', // ข้อความสำหรับปุ่มแรก
							    'NO' // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
							)
						)
					)
					);
				    break;  
			        case "tc":
						// กำหนด action 4 ปุ่ม 4 ประเภท
						$actionBuilder = array(
							new MessageTemplateActionBuilder(
								'Message Template',// ข้อความแสดงในปุ่ม
								'This is Text' // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
							),
							new UriTemplateActionBuilder(
								'Uri Template', // ข้อความแสดงในปุ่ม
								'https://www.seagull-brand.com'
							),
							new PostbackTemplateActionBuilder(
								'Postback', // ข้อความแสดงในปุ่ม
								http_build_query(array(
									'action'=>'buy',
					 
									'item'=>100
								)), // ข้อมูลที่จะส่งไปใน webhook ผ่าน postback event
								'Postback Text'  // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
							),      
						);
						$replyData = new TemplateMessageBuilder('Carousel',
							new CarouselTemplateBuilder(
								array(
									new CarouselColumnTemplateBuilder(
										'Title Carousel',
										'Description Carousel',
										'https://www.seagull-brand.com/tssphpbot/linebot/imgsrc/photos/f/simpleflower/700',
										$actionBuilder
									),
									new CarouselColumnTemplateBuilder(
										'Title Carousel',
										'Description Carousel',
										'https://www.seagull-brand.com/tssphpbot/linebot/imgsrc/photos/f/simpleflower/700',
										$actionBuilder
									),
									new CarouselColumnTemplateBuilder(
										'Title Carousel',
										'Description Carousel',
										'https://www.seagull-brand.com/tssphpbot/linebot/imgsrc/photos/f/simpleflower/700',
										$actionBuilder
									),                                          
								)
							)
						);
						break;  
				default:
                    
					if (is_numeric($userMessage) AND strlen($userMessage) == 6 ){
						$textReplyMessage = " คุณลงทะเบียนสำเร็จ ";
                    	$replyData = new TextMessageBuilder($textReplyMessage);         	
					}

					else	
					{
                    	$textReplyMessage = " คุณไม่ได้พิมพ์ ค่า ตามที่กำหนด";
                    	$replyData = new TextMessageBuilder($textReplyMessage);         
					}
                    break;                                          
            }
            break;
        default:
            $textReplyMessage = " กรุณาใส่เลขที่พนักงาน 6 หลัก(Ex. 430184) ";//json_encode($events);
			$replyData = new TextMessageBuilder($textReplyMessage);  
            break;  
    }
}

// ส่วนของคำสั่งตอบกลับข้อความ

$response = $bot->replyMessage($replyToken,$replyData);
if ($response->isSucceeded()) {
    echo 'Succeeded!';
    return;
}
 
// Failed
echo $response->getHTTPStatus() . ' ' . $response->getRawBody();
?>