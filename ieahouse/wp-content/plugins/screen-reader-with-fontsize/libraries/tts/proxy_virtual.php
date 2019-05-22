<?php
/**
 * Proxy della REST API di Google TTS
 * @package SCREENREADER::plugins
 * @author JExtensions Store 
 * @copyright (C) 2016 - JExtensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
ini_set ( 'display_errors', false );

// testo
$text = preg_replace ( "/[" . PHP_EOL . "]+/", " ", ($_GET ['text']) );
// lingua
$lang = ($_GET ['lang']);
// security token same domain
$token = ($_GET ['token']);
// engine token if used by the engine
$engineToken = ($_GET ['engine_token']);
// default php session lifetime
session_start();
$defaultPHPSessionLifetime = 1440;

if(!function_exists('curl_version')) {
	exit();
}

if ($token === md5 ( $_SERVER ['HTTP_HOST'] )) {
	// Random user agents DB
	$userAgents = array (
			"Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0",
			"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10; rv:33.0) Gecko/20100101 Firefox/33.0",
			"Mozilla/5.0 (X11; Linux i586; rv:31.0) Gecko/20100101 Firefox/31.0",
			"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:31.0) Gecko/20130401 Firefox/31.0",
			"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36",
			"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36",
			"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1944.0 Safari/537.36",
			"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2224.3 Safari/537.36",
			"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2224.3 Safari/537.36",
			"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A",
			"Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25",
			"Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; AS; rv:11.0) like Gecko",
			"Mozilla/5.0 (compatible, MSIE 11, Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko",
			"Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)",
			"Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)",
			"Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/5.0)",
			"Mozilla/5.0 (compatible; MSIE 10.0; Macintosh; Intel Mac OS X 10_7_3; Trident/6.0)"
		);
	$ua = $userAgents [rand ( 0, count ( $userAgents ) - 1 )];

	if(!isset($_SESSION['scr_reader_vs_phpsessid']) || (time() - $_SESSION['scr_reader_vs_phpsessid_lifetime']) > $defaultPHPSessionLifetime) {
		// Remote GET using CURL lib to retrieve the PHPSESSID header cookie
		$ch = curl_init();
		$url = "http://www.acapela-group.com";
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$headers = array (
				'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
				'Accept-Encoding: gzip, deflate, sdch',
				'Host: www.acapela-group.com',
				'Cache-Control: no-cache',
				'Connection: keep-alive',
				'Pragma: no-cache',
				'User-Agent: ' . $ua,
				'Accept-Language: en-US,en;q=0.8,de;q=0.6,es;q=0.4,fr;q=0.2,it;q=0.2,ru;q=0.2,ja;q=0.2,el;q=0.2,sk;q=0.2,nl;q=0.2,ar;q=0.2,sv;q=0.2'
		);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		// execute post
		$HTTPResponse = new stdClass();
		$HTTPResponse->body = curl_exec ($ch);
		// close the connection
		curl_close ($ch);
		
		// Grab the session ID
		$PHPSessionID = preg_match('/PHPSESSID=([a-zA-Z0-9,-]+)/i', $HTTPResponse->body, $matches);
		if($PHPSessionID) {
			$PHPSessionID = $matches[1];
		} else {
			exit();
		}
		
		// Store session variabled
		$_SESSION['scr_reader_vs_phpsessid'] = $PHPSessionID;
		$_SESSION['scr_reader_vs_phpsessid_lifetime'] = time();
	} else {
		$PHPSessionID = $_SESSION['scr_reader_vs_phpsessid'];
	}
	
	// Mapped language to code
	$mappedLangCode = array (
			'ar-AR' => array('sonid0', 'Leila'),
			'ar-AE' => array('sonid0', 'Leila'),
			'ar-AA' => array('sonid0', 'Leila'),
			'ar' => array('sonid0', 'Leila'),
			'en-US' => array('sonid10', 'Karen'),
			'en-GB' => array('sonid9', 'Graham'),
			'en' => array('sonid9', 'Graham'),
			'fr-FR' => array('sonid15', 'Alice'),
			'fr' => array('sonid15', 'Alice'),
			'fi-FI' => array('sonid12', 'Sanna'),
			'fi' => array('sonid12', 'Sanna'),
			'de-DE' => array('sonid16', 'Andreas'),
			'de' => array('sonid16', 'Andreas'),
			'es-ES' => array('sonid28', 'Maria'),
			'es' => array('sonid28', 'Maria'),
			'it-IT' => array('sonid18', 'Fabiana'),
			'it' => array('sonid18', 'Fabiana'),
			'nl-BE' => array('sonid4', 'Jeroen'),
			'nl-NL' => array('sonid5', 'Daan'),
			'nl' => array('sonid5', 'Daan'),
			'ca-ES' => array('sonid1', 'Laia'),
			'ca' => array('sonid1', 'Laia'),
			'cs-CZ' => array('sonid2', 'Eliska'),
			'cs' => array('sonid2', 'Eliska'),
			'da-DK' => array('sonid3', 'Mette'),
			'da' => array('sonid3', 'Mette'),
			'el-GR' => array('sonid17', 'Dimitris'),
			'el' => array('sonid17', 'Dimitris'),
			'ja-JP' => array('sonid19', 'Sakura'),
			'ja' => array('sonid19', 'Sakura'),
			'ko-KR' => array('sonid20', 'Minji'),
			'ko' => array('sonid20', 'Minji'),
			'nb-NO' => array('sonid22', 'Bente'),
			'nb' => array('sonid22', 'Bente'),
			'pl-PL' => array('sonid23', 'Ania'),
			'pl' => array('sonid23', 'Ania'),
			'pt-PT' => array('sonid25', 'Celia'),
			'pt-BR' => array('sonid24', 'Marcia'),
			'pt' => array('sonid25', 'Celia'),
			'ru-RU' => array('sonid26', 'Alyona'),
			'ru' => array('sonid26', 'Alyona'),
			'sv-FI' => array('sonid30', 'Emma'),
			'sv-SE' => array('sonid30', 'Emma'),
			'sv' => array('sonid30', 'Emma'),
			'tr-TR' => array('sonid34', 'Ipek'),
			'tr' => array('sonid34', 'Ipek'),
			
	);
	if(array_key_exists($lang, $mappedLangCode)) {
		$langNumericKey = $mappedLangCode[$lang][0];
		$langSelectedVoice = $mappedLangCode[$lang][1];
	} else {
		$langNumericKey = 'sonid9'; // Fallback always on english
		$langSelectedVoice = 'Graham';
	}
	
	// Make the first POST form request and get the HTML page including the MP3 link
	$qs = array (
			'MyLanguages' => $langNumericKey,
			'MySelectedVoice' => $langSelectedVoice,
			'MyTextForTTS' => ($text),
			'agreeterms' => 'on',
			't' => 1,
			'SendToVaaS' => ''
	);
	
	// Remote POST using sockets or CURL lib
	$ch = curl_init();
	$url = "http://www.acapela-group.com/demo-tts/DemoHTML5Form_V2.php";
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $qs);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
	$headers = array (
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
			'Accept-Encoding: gzip, deflate',
			'Accept-Language: en-US,en;q=0.8,de;q=0.6,es;q=0.4,fr;q=0.2,it;q=0.2,ru;q=0.2,ja;q=0.2,el;q=0.2,sk;q=0.2,nl;q=0.2,ar;q=0.2,sv;q=0.2',
			'Cache-Control: no-cache',
			'Connection: keep-alive',
			'Cookie: PHPSESSID=' . $PHPSessionID,
			'Host: www.acapela-group.com',
			'Origin: http://www.acapela-group.com',
			'Pragma: no-cache',
			'Referer: http://www.acapela-group.com/demo-tts/DemoHTML5Form_V2.php' 
	);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	
	// execute post
	$HTTPResponse = new stdClass();
	$HTTPResponse->body = curl_exec ($ch);
	
	// close the connection
	curl_close ($ch);
		
	// Now scrape the javascript var for the Mp3 file generated
	$mp3BinaryFile = preg_match("/(myPhpVar)\s=\s'(.*)'/i", $HTTPResponse->body, $matches);
	// Found a valid Mp3 file to play?
	if($mp3BinaryFile) {
		$opts = array(
				'http'=>array(
						'method'=>"GET",
						'header'=>"Accept-language: en\r\n" .
								  "Host: h-ir-ssd-1.acapela-group.com\r\n" .
								  "Cache-Control: no-cache\r\n" .
								  "Connection: keep-alive\r\n" .
								  "Pragma: no-cache\r\n" .
								  "Range: bytes=0-\r\n" .
								  "User-Agent: $ua\r\n" .
								  "Accept: */*\r\n" .
								  "Referer: http://www.acapela-group.com/virtual-speaker/demo-tts/DemoHTML5Form_virtualspeakerV2.php\r\n" .
								  "Accept-Language: en-US,en;q=0.8,de;q=0.6,es;q=0.4,fr;q=0.2,it;q=0.2,ru;q=0.2,ja;q=0.2,el;q=0.2,sk;q=0.2,nl;q=0.2,ar;q=0.2,sv;q=0.2'"
				)
		);
		
		$context = stream_context_create($opts);
		
		$binaryString = file_get_contents ($matches[2], false, $context );
		
		// If fopen is not enabled, fallback on the socket HTTP GET
		if(!$binaryString) {
			$socketHeadersArray = array("Accept-language" => "en",
					"Cache-Control" => "no-cache",
					"Connection" => "keep-alive",
					"Pragma" => "no-cache",
					"Range" => "bytes=0-",
					"User-Agent" => $ua,
					"Accept" => "*/*",
					"Referer" => "http://www.acapela-group.com/virtual-speaker/demo-tts/DemoHTML5Form_virtualspeakerV2.php",
					"Accept-Language" => "en-US,en;q=0.8,de;q=0.6,es;q=0.4,fr;q=0.2,it;q=0.2,ru;q=0.2,ja;q=0.2,el;q=0.2,sk;q=0.2,nl;q=0.2,ar;q=0.2,sv;q=0.2");
			require_once '../http/http.php';
			$HTTPClient = new jscrHttp ();
			$binaryString = $HTTPClient->get($matches[2], $socketHeadersArray)->body;
		}
		
		$download_size = strlen ( $binaryString );
	}
	
	
	// send the headers
	header ( "Pragma: public" ); // purge the browser cache
	header ( "Expires: 0" ); // ...
	header ( "Cache-Control:" ); // ...
	header ( "Cache-Control: public" ); // ...
	header ( "Content-Description: File Transfer" ); //
	header ( "Content-Type: audio/mpeg" ); // file type
	header ( "Content-Disposition: attachment; filename=tts.mp3" );
	header ( "Content-Transfer-Encoding: binary" ); // transfer method
	header ( "Content-Length: $download_size" ); // download length
	
	echo $binaryString;
}
exit ();?>