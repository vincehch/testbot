<?php
 $json_str = file_get_contents('php://input'); //接收REQUEST的BODY
 $json_obj = json_decode($json_str); //轉JSON格式
 //產生回傳給line server的格式
 $sender_userid = $json_obj->events[0]->source->userId;
 $sender_txt = $json_obj->events[0]->message->text;
 $sender_replyToken = $json_obj->events[0]->replyToken;
 $line_server_url = 'https://api.line.me/v2/bot/message/push';
 //用sender_txt來分辨要發何種訊息
 switch ($sender_txt) {
    		case "push":
        		$response = array (
				"to" => $sender_userid,
				"messages" => array (
					array (
						"type" => "text",
						"text" => "Hello, YOU SAY ".$sender_txt
					)
				)
			);
        		break;
    		case "reply":
			$line_server_url = 'https://api.line.me/v2/bot/message/reply';
        		$response = array (
				"replyToken" => $sender_replyToken,
				"messages" => array (
					array (
						"type" => "text",
						"text" => "Hello, YOU SAY ".$sender_txt
					)
				)
			);
        		break;
		case "image":
			$line_server_url = 'https://api.line.me/v2/bot/message/reply';
        		$response = array (
				"replyToken" => $sender_replyToken,
				"messages" => array (
					array (
						"type" => "image",
						"originalContentUrl" => "https://www.w3schools.com/css/paris.jpg",
						"previewImageUrl" => "https://www.nasa.gov/sites/default/themes/NASAPortal/images/feed.png"
					)
				)
			);
        		break;
 }
 $myfile = fopen("log.txt","w+") or die("Unable to open file!"); //設定一個log.txt 用來印訊息
 fwrite($myfile, "\xEF\xBB\xBF".json_encode($response)); //在字串前加入\xEF\xBB\xBF轉成utf8格式
 fclose($myfile);
 //回傳給line server
 $header[] = "Content-Type: application/json";
 $header[] = "Authorization: Bearer B6qIEPkUfRg0n304IHX/wtuFQ4S2Nga/ZZJC4o4Ej0nofTSA9O0Yn/BOWP/2zubHV8xiSmpcStJc8ELXckGit/BG/ThWsUQbNW0Mfu2/NOBni9+QlIhBjWoaXSRyIMPjRMQ+BpwmH7vpGt8TTL541AdB04t89/1O/w1cDnyilFU=";
 $ch = curl_init($line_server_url);                                                                      
 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
 curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));                                                                  
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
 curl_setopt($ch, CURLOPT_HTTPHEADER, $header);                                                                                                   
 $result = curl_exec($ch);
 curl_close($ch); 
?>
