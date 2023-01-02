<?php
date_default_timezone_set('GMT');
//require_once 'gzip_pres.php';
ob_start();
session_start();
require_once 'Class/Db.Class.php';
require_once 'Function.php';
$uri = $_SERVER['REQUEST_URI'];
$uris = explode('/', $uri);
if($uris[count($uris)-1] !='sitemap'){
    require_once 'Class/Site.Class.php';
    $Site = new Site();
}
/////////////////////////////////////////////////////////
$server = "localhost";
$veritabani = "cevikbilisim_xpeedzero";
$kullanici = "cevikbilisim_yakup";
$sifre = "d04A7amFjq_yakup";
/////////////////////////////////////////////////////////
$db = new Db($server,$veritabani,$kullanici,$sifre);
$baglanti = @mysql_connect($server, $kullanici, $sifre);
$veritabani = @mysql_select_db($veritabani);

global $db;
$timezone = $db->select('site_ayarlari')
    ->where('ayarAdi','timezone')
    ->run(true);
date_default_timezone_set($timezone['ayarText']);
$itemsPerPage = 20;