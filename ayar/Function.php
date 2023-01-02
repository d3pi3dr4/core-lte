<?php

// Yönlendirme Fonksiyonu
function git($url,$zaman = 0){
    if($zaman == "0"){
		echo "<meta http-equiv='refresh' content=0;URL=".$url.">";
    }else{
		echo "<meta http-equiv='refresh' content=".$zaman.";URL=".$url.">";
    }
}

// Session Fonksiyonu
function session ($par){
    return $_SESSION[$par];
}

// Sef-Link Fonksiyonu
function sef_link($string){
    $find = array('Ç', 'Ş', 'Ğ', 'Ü', 'İ', 'Ö', 'ç', 'ş', 'ğ', 'ü', 'ö', 'ı', '+', '#');
    $replace = array('c', 's', 'g', 'u', 'i', 'o', 'c', 's', 'g', 'u', 'o', 'i', 'plus', 'sharp');
    $string = strtolower(str_replace($find, $replace, $string));
    $string = preg_replace("@[^A-Za-z0-9\-_\.\+]@i", ' ', $string);
    $string = trim(preg_replace('/\s+/', ' ', $string));
    $string = str_replace(' ', '-', $string);
    return $string;
}

// Post ve Get Temizleme Fonksiyonu
function alt_replace($string){
    $search = array(
        chr(0xC2) . chr(0xA0), // c2a0; Alt+255; Alt+0160; Alt+511; Alt+99999999;
        chr(0xC2) . chr(0x90), // c290; Alt+0144
        chr(0xC2) . chr(0x9D), // cd9d; Alt+0157
        chr(0xC2) . chr(0x81), // c281; Alt+0129
        chr(0xC2) . chr(0x8D), // c28d; Alt+0141
        chr(0xC2) . chr(0x8F), // c28f; Alt+0143
        chr(0xC2) . chr(0xAD), // cdad; Alt+0173
        chr(0xAD)
    );
    $string = str_replace($search, '', $string);
    return trim($string);
}

function post($name){
    if (isset($_POST[$name])) {
        if (is_array($_POST[$name])) {
            return array_map(function ($item) {
                return htmlspecialchars(trim(alt_replace($item)));
            }, $_POST[$name]);
        } else {
            return htmlspecialchars(trim(alt_replace($_POST[$name])));
        }
    }
}

function get($name){
    if (isset($_GET[$name])) {
        if (is_array($_GET[$name])) {
            return array_map(function ($item) {
                return htmlspecialchars(trim(alt_replace($item)));
            }, $_GET[$name]);
        } else {
            return htmlspecialchars(trim(alt_replace($_GET[$name])));
        }
    }
}

// Yazı Kısaltma Fonsiyonu
function kisalt($kelime, $str = 10){
    if (strlen($kelime) > $str){
        if (function_exists("mb_substr")) $kelime = mb_substr($kelime, 0, $str, "UTF-8").'..';
        else $kelime = substr($kelime, 0, $str).'..';
    }
    return $kelime;
}

// Url
function URL(){
    $dizin = $_SERVER["HTTP_HOST"];
    return 'http://'.$dizin.'/';
}

// Tarih Fonsiyonu
//gün,ay,yil,format ekle. parametresiz bugünü verir  //yyyy-mm-dd
function tarih($gunekle=0,$ayekle=0,$yilekle=0, $format='d.m.Y')
{
  $zaman = date($format ,mktime(0, 0, 0, date("m")+$ayekle, date("d")+$gunekle, date("Y")+$yilekle));
  return $zaman;
}

function tarihkarsilastir($ilktarih, $ikincitarih)	// ilk girilen tarih en yeni zamansa 0 değilse 1 verir  bugün date("Y-m-d")
	{
		if(strtotime($ilktarih) >= strtotime($ikincitarih))
			return 0;
		else
			return 1;
	}
	
// Ip fonksiyonu
  function GetIP(){
	if(getenv("HTTP_CLIENT_IP")) {
 		$ip = getenv("HTTP_CLIENT_IP");
 	} elseif(getenv("HTTP_X_FORWARDED_FOR")) {
 		$ip = getenv("HTTP_X_FORWARDED_FOR");
 		if (strstr($ip, ',')) {
 			$tmp = explode (',', $ip);
 			$ip = trim($tmp[0]);
 		}
 	} else {
 	$ip = getenv("REMOTE_ADDR");
 	}
	return $ip;
}
  //yüklenen resmi gönderdiğimizde mb cinsinden float olarak boyutunu veriyor
  function resimboyut($resim)
  {
    return $resim['size']/1048576;
  }

// Lisans Sistemi - 15:30 30.4.2019 - Yakup ÇEVİK - www.showyazilim.com
// Proje Tasdix ile tasdiklenmiştir. Tüm yasal hakları Show Yazılım 'a aittir.
?>

