<?php

Class Site{

    /*
     * Site Ayarlarına Erişme
     * 1 Parametre Alır. O da Ayar Adı*/
    public function site_ayarlari($par){
        global $db;
        $site_ayarlari = $db->select('site_ayarlari')
            ->where('ayarAdi',$par)
            ->run(true);
        return $site_ayarlari['ayarText'];
    }
    public function getSiteSettings()
    {
        global $db;
        $settings = $db->select('site_ayarlari')
            ->run();
        return $settings;
    }

    public function getSiteSetting($data, $par)
    {
        foreach ($data as $item) {
            if ($item['ayarAdi'] == $par){
                return $item['ayarText'];
            }
        }
        return false;
    }

    public function getMenuler()
    {
        global $db;
        $menuler = $db->select('menuler')
            ->orderby('ust_id', 'ASC')
            ->orderby('sira', 'ASC')
            ->run();
        return $menuler;
    }


    public function getAyarlar($ayar_ad, $ayar_tip)
    {
        global $db;
        $ayarlar = $db->select('ayarlar')
            ->where('ayar_ad', $ayar_ad)
            ->where('ayar_tip', $ayar_tip)
            ->run();

        if($ayarlar[0]['ayar_deger'] != ''){
            return $ayarlar[0]['ayar_deger'];
        } else {
            if ($ayar_tip == 'siralama')
                return 'ASC';
            else
                return '20';
        }
    }


    public function modul_ayarlari($par){
        global $db;
        $modul_ayarlari = $db->select('modul_ayarlari')
            ->where('id',1)
            ->run(true);
            return $modul_ayarlari[$par];
    }

    public function urunler($par){
        global $db;

        $pdo = $db->prepare("SELECT * FROM cat_urun JOIN urunler ON cat_urun.cid = :gelen
                                            WHERE urunler.kategori_id = cat_urun.uid ORDER BY sira ASC LIMIT :baslangic, :bitis");
        $pdo->bindParam(':gelen', $par['gelen'], PDO::PARAM_INT);
        $pdo->bindParam(':baslangic', $par['baslangic'], PDO::PARAM_INT);
        $pdo->bindParam(':bitis', $par['bitis'], PDO::PARAM_INT);
        $pdo->execute();
        $urunList=$pdo->fetchAll(PDO::FETCH_ASSOC);

        $data = array();
        foreach ($urunList as $urun){
            if(strtotime($urun['tarih_zamanlama']." ".$urun['saat_zamanlama'])<time()) {
                $data[] = $urun;
            }
        }
        return $data;
    }
    public function urunSayisi($gelen){
        global $db;

        $pdo = $db->prepare("SELECT Count(*) as toplam FROM cat_urun JOIN urunler ON cat_urun.cid = :gelen
                                            WHERE urunler.kategori_id = cat_urun.uid ORDER BY sira ASC ");
        $pdo->bindParam(':gelen', $gelen, PDO::PARAM_INT);
        $pdo->execute();
        $urunList=$pdo->fetchAll(PDO::FETCH_ASSOC);

        return $urunList[0]['toplam'];
    }

    public function projeSayisi(){
        global $db;

        $toplam = $db->select('projeler')

            ->from('count(id) as total')
            ->total();
        return $toplam;





    }

    /*
     * Kategorileri Listeleme*/
    public function kategori_listeleadmin(){
        global $db;
        $dataList = array();
        $rowCount = 0;
        $kategori_listele = $db->select('sayfalar')
            ->where('sayfa_type',1)
            ->run();
        foreach($kategori_listele as $kategoriler){
            $dataList[$rowCount] = array('sayfa_baslik' => $kategoriler['sayfa_baslik'],'sayfa_seflink' => $kategoriler['sayfa_seflink'],'sayfa_id'  => $kategoriler['sayfa_id'],'aktifkontrol'  => $kategoriler['aktifkontrol']);
            $rowCount++;
        }
        return $dataList;
    }

    /*
     * Kategorileri Listeleme*/
    public function diger_kategori_listeleadmin($sayfatype){
        global $db;
        $dataList = array();
        $rowCount = 0;
        $kategori_listele = $db->select('sayfalar')
            ->where('sayfa_type',$sayfatype)
            ->run();
        foreach($kategori_listele as $kategoriler){
            $dataList[$rowCount] = array('sayfa_baslik' => $kategoriler['sayfa_baslik'],'sayfa_seflink' => $kategoriler['sayfa_seflink'],'sayfa_id'  => $kategoriler['sayfa_id'],'aktifkontrol'  => $kategoriler['aktifkontrol']);
            $rowCount++;
        }
        return $dataList;
    }


    public function search($gelen){
        global $db;
        $query = $db->query("SELECT * FROM urunler WHERE urunkodu LIKE '%$gelen%' OR urun_text LIKE '%$gelen%' OR urun_baslik LIKE '%$gelen%'", PDO::FETCH_ASSOC);
        return $query;
    }

    /*
     * Kategorileri Listeleme*/
    public function kategori_listele(){
        global $db;
        $dataList = array();
        $kategori_listele = $db->select('sayfalar')
            ->where('top',0)
            ->orderby('sira','ASC')
            ->where('sayfa_type',1)
            ->run();
        foreach($kategori_listele as $kategoriler){
            $prefix = substr($kategoriler['hizmeticon'],0,1);
            $suffix = substr($kategoriler['hizmeticon'],1);
             if($prefix=='{'){
                 $icon = '<i class="fa '.$suffix.'" aria-hidden="true"></i>';
             } elseif($prefix=='}'){
                 $icon ='<img src="upload/'.$suffix.'" height="32" width="32">';
             }else {
                 $icon = '<i class="fa fa-star" aria-hidden="true"></i>';
             }

            $dataList[] = array(
                 'sayfa_baslik' => $kategoriler['sayfa_baslik'],
                 'sayfa_seflink' => $kategoriler['sayfa_seflink'],
                 'sayfa_id'  => $kategoriler['sayfa_id'],
                 'icon'=>$icon,
                 'aktifkontrol'  => $kategoriler['aktifkontrol']);
        }
        return $dataList;
    }

    public function kategori_listele2($a){
        global $db;
        $dataList = array();
        $rowCount = 0;
        $kategori_listele = $db->select('sayfalar')
            ->where('top',$a)
            ->run();
        foreach($kategori_listele as $kategoriler){
            $prefix = substr($kategoriler['hizmeticon'],0,1);
            $suffix = substr($kategoriler['hizmeticon'],1);
             if($prefix=='{'){
                 $icon = '<i class="fa '.$suffix.'" aria-hidden="true"></i>';
             } elseif($prefix=='}'){
                 $icon ='<img src="upload/'.$suffix.'" height="32" width="32">';
             }else {
                 $icon = '<i class="fa fa-star" aria-hidden="true"></i>';
             }

            $dataList[$rowCount] = array(
                'sayfa_baslik' => $kategoriler['sayfa_baslik'],
                'sayfa_seflink' => $kategoriler['sayfa_seflink'],
                'sayfa_id'  => $kategoriler['sayfa_id'],
                'icon'=>$icon,
                'aktifkontrol'  => $kategoriler['aktifkontrol']);
            $rowCount++;
        }
        return $dataList;
    }

    /*
     * Kategoriye Göre Ürün Listeleme*/
    public function kategoriler_urun($gelen){
        global $db;
        $dataList = array();
        $rowCount = 0;
        $urun_listele = $db->select('urunler')
            ->where('kategori_id',$gelen)
            ->orderby('urun_id','DESC')
            ->run();

        if($urun_listele){
            foreach($urun_listele as $urunler){
              $tarih = $urunler['tarih_zamanlama'];
              $saat = $urunler['saat_zamanlama'];
              if(strtotime($tarih." ".$saat)<time())
                {
                $dataList[$rowCount] = array(
                    'urun_baslik'         => $urunler['urun_baslik'],
					'urun_fiyat'          => $urunler['urun_fiyat'],
					'urun_seflink'          => $urunler['urun_seflink'],
                    'urun_resim'          => $urunler['urun_resim'],
                    'urun_tarih'          => $urunler['urun_tarih'],
                    'urun_okunma'         => $urunler['urun_okunma'],
                    'kategori_id'           => $urunler['kategori_id'],
                    'urun_id'             => $urunler['urun_id'],
                    'urun_seo_etiket'     => $urunler['urun_seo_etiket'],
                    'urun_seo_aciklama'   => $urunler['urun_seo_aciklama'],
                    'urun_text'           => $urunler['urun_text'],
                    'anasayfa_resim'      => $urunler['anasayfa_resim'],
                    'fiyat_goster'        => $urunler['fiyat_goster'],
					'urun_pdf'           => $urunler['urun_pdf'],
					'urun_video'           => $urunler['urun_video'],
                );
                $rowCount++;
            }}
        }else{
            echo '<center><br><br><br> <b><span style=font-size:30px; class=ylow> Henüz Kategoriye Ait Ürün Eklenmemiş!</span></b> <br><br><i class="fa fa-frown-o ylow" style=font-size:150px;> </i></center><br><br>';
        }
        return $dataList;
    }

    public function kategoriler_urun_matte($gelen){
        global $db;
        $dataList = array();
        $rowCount = 0;
        $urun_listele = $db->select('urunler')
            ->where('kategori_id',$gelen)
            ->orderby('urun_id','DESC')
            ->run();
        if($urun_listele){
            foreach($urun_listele as $urunler){
                $dataList[$rowCount] = array(
                    'urun_baslik'         => $urunler['urun_baslik'],
					'urun_fiyat'          => $urunler['urun_fiyat'],
					'urun_seflink'          => $urunler['urun_seflink'],
                    'urun_resim'          => $urunler['urun_resim'],
                    'urun_tarih'          => $urunler['urun_tarih'],
                    'urun_okunma'         => $urunler['urun_okunma'],
                    'kategori_id'           => $urunler['kategori_id'],
                    'urun_id'             => $urunler['urun_id'],
                    'urun_seo_etiket'     => $urunler['urun_seo_etiket'],
                    'urun_seo_aciklama'   => $urunler['urun_seo_aciklama'],
                    'urun_text'           => $urunler['urun_text'],
                    'anasayfa_resim'      => $urunler['anasayfa_resim'],
                    'fiyat_goster'        => $urunler['fiyat_goster'],
					'urun_pdf'           => $urunler['urun_pdf'],
					'urun_video'           => $urunler['urun_video'],
                );
                $rowCount++;
            }
        }else{
            echo '<center><br><br><br> <b><span style=font-size:30px; class=ylow> Henüz Kategoriye Ait Ürün Eklenmemiş!</span></b> <br><br><i class="fa fa-frown-o ylow" style=font-size:150px;> </i></center><br><br>';
        }
        return $dataList;
    }

	    /*
     * Sayfaları Listeleme*/
    public function sayfa_listele(){
        global $db;
        $dataList = array();
        $rowCount = 0;
        $sayfa_listele = $db->select('sayfalar')
            ->orderby('sira','ASC')
            ->where('sayfa_type',0)
            ->run();
        foreach($sayfa_listele as $sayfalar){
            $dataList[$rowCount] = array('sayfa_baslik' => $sayfalar['sayfa_baslik'],'sayfa_seflink' => $sayfalar['sayfa_seflink'],'sayfa_id'  => $sayfalar['sayfa_id'],'aktifkontrol'  => $sayfalar['aktifkontrol'] );
            $rowCount++;
        }
        return $dataList;
    }

    /* sss home Listeleme*/

       public function sss_home_listele(){
             global $db;
             $dataList = array();
             $rowCount = 0;
             $limit = $this->getAyarlar('anasayfa_sss', 'listesayisi');
             $orderby = $this->getAyarlar('anasayfa_sss', 'siralama');


             $sss_home_listele = $db->select('sayfalar')
             ->LIMIT(0,$limit)
                   ->orderby('sira',$orderby)
                 ->where('sayfa_type',2)

                 ->run();

             foreach($sss_home_listele as $hizmetler){

                 $dataList[$rowCount] = array(
           'sayfa_baslik' => $hizmetler['sayfa_baslik'],
           'hizmeticon' => $hizmetler['hizmeticon'],
           'sayfa_text' => $hizmetler['sayfa_text'],
           'sayfa_seflink' => $hizmetler['sayfa_seflink'],
           'sayfa_id'  => $hizmetler['sayfa_id'],
           'aktifkontrol'  => $hizmetler['aktifkontrol']);

                 $rowCount++;

             }

             return $dataList;

         }
       /* Hizmet Listeleme*/


	/* Hizmet Listeleme*/

  public function hizmetler_listele(){

        global $db;

        $dataList = array();

        $rowCount = 0;

        $hizmetler_listele = $db->select('sayfalar')
              ->orderby('sira','ASC')
            ->where('sayfa_type',2)

            ->run();

        foreach($hizmetler_listele as $hizmetler){

            $dataList[$rowCount] = array(
			'sayfa_baslik' => $hizmetler['sayfa_baslik'],
			'hizmeticon' => $hizmetler['hizmeticon'],
			'sayfa_text' => $hizmetler['sayfa_text'],
			'sayfa_seflink' => $hizmetler['sayfa_seflink'],
			'sayfa_id'  => $hizmetler['sayfa_id'],
      'aktifkontrol'  => $hizmetler['aktifkontrol']);

            $rowCount++;

        }

        return $dataList;

    }
/* Hizmet Listeleme*/


/* Hizmet 2 Listeleme*/

  public function hizmetler2_listele(){

        global $db;

        $dataList = array();

        $rowCount = 0;

        $hizmetler2_listele = $db->select('sayfalar')
            ->orderby('sira','ASC')
            ->where('sayfa_type',3)

            ->run();

        foreach($hizmetler2_listele as $hizmetler2){

          $tarih = $hizmetler2['tarih_zamanlama'];
          $saat = $hizmetler2['saat_zamanlama'];
          if(strtotime($tarih." ".$saat)<time())
            {
            $dataList[$rowCount] = array(
			'sayfa_baslik' => $hizmetler2['sayfa_baslik'],
			'hizmeticon' => $hizmetler2['hizmeticon'],
			'sayfa_text' => $hizmetler2['sayfa_text'],
			'sayfa_title' => $hizmetler2['sayfa_title'],
			'sayfa_aciklama' => $hizmetler2['sayfa_aciklama'],
			'sayfa_etiket' => $hizmetler2['sayfa_etiket'],
			'sayfa_seflink' => $hizmetler2['sayfa_seflink'],
      'aktifkontrol' => $hizmetler2['aktifkontrol'],
			'sayfa_id'  => $hizmetler2['sayfa_id']);

            $rowCount++;

        }}

        return $dataList;

    }
/* Hizmet Listeleme*/


/*

     * Eklenen Blog Listesi*/

    public function blog_listele(){

        global $db;
        //$homepagesetting = $this->getHomepageSetting();
        $limit = $this->getAyarlar('anasayfa_bloglar', 'listesayisi');
        $orderby = $this->getAyarlar('anasayfa_bloglar', 'siralama');
        $dataList = array();
        $rowCount = 0;

        $blog_listele = $db->select('blog')
            ->LIMIT(0,$limit)
            ->orderby('blog_id',$orderby)
            ->run();

        foreach($blog_listele as $blog){

            $dataList[$rowCount] = array(
                'blog_baslik'         => $blog['blog_baslik'],
                'anasayfa_resim'          => $blog['anasayfa_resim'],
                'blog_tarih'          => $blog['blog_tarih'],
                'blog_okunma'         => $blog['blog_okunma'],
                'blog_id'             => $blog['blog_id'],
                'blog_seo_etiket'     => $blog['blog_seo_etiket'],
                'blog_seo_aciklama'   => $blog['blog_seo_aciklama'],
                'blog_text'           => $blog['blog_text'],
                'blog_seflink'        => $blog['blog_seflink'],
                'aktifkontrol'        => $blog['aktifkontrol'],
            );

            $rowCount++;


        }

        return $dataList;

    }

/*

     * Eklenen Blog Listesi*/

    public function blog_listele2(){

        global $db;

        $dataList = array();


        $rowCount = 0;

        $blog_listele2 = $db->select('blog')


            ->orderby('blog_id','DESC')


            ->run();

        foreach($blog_listele2 as $blog){
          if(tarihkarsilastir(date("Y-m-d"), $blog['tarih_zamanlama'])==0){
            if(tarihkarsilastir(date('H:i'), $blog["saat_zamanlama"])==0)
            {
            $dataList[$rowCount] = array(

                'blog_baslik'         => $blog['blog_baslik'],

                'anasayfa_resim'          => $blog['anasayfa_resim'],

                'blog_tarih'          => $blog['blog_tarih'],

                'blog_okunma'         => $blog['blog_okunma'],

                'blog_id'             => $blog['blog_id'],

                'blog_seo_etiket'     => $blog['blog_seo_etiket'],

                'blog_seo_aciklama'   => $blog['blog_seo_aciklama'],

                'blog_text'           => $blog['blog_text'],

                'blog_seflink'        => $blog['blog_seflink'],

            );

            $rowCount++;

          }
        }
        }


        return $dataList;

    }


	    /*

     * Eklenen blog Bilgileri

     * Değere Göre İşlem Yapılır*/

    public function blog($gelen,$par){

        global $db;

        $blog = $db->select('blog')

            ->where('blog_seflink',$gelen)

            ->run(true);

        return $blog[$par];

    }


    /*
     * Sayfa Ürünlerine Ulaşma*/
    public function sayfa($gelen,$par){
        global $db;
        $sayfa = $db->select('sayfalar')
            ->where('sayfa_seflink',$gelen)
            ->run(true);
        return $sayfa[$par];
    }
    public function sayfad($gelen,$par){
        global $db;
        $sayfa = $db->select('sayfalar')
            ->where('sayfa_id',$gelen)
            ->run(true);
        return $sayfa[$par];
    }



    /*
     * Eklenen Ürün Listesi*/
    public function urun_listele(){
        global $db;
        $dataList = array();
        $rowCount = 0;

       // $homepagesetting = $this->getHomepageSetting();
        $limit = $this->getAyarlar('anasayfa_urunler', 'listesayisi');
        $orderby = $this->getAyarlar('anasayfa_urunler', 'siralama');

        $urun_listele = $db->select('urunler')
            ->orderby('sira', $orderby)
            ->limit(0, $limit)
            ->run();
        foreach($urun_listele as $urunler){
          $tarih = $urunler['tarih_zamanlama'];
          $saat = $urunler['saat_zamanlama'];
          if(strtotime($tarih." ".$saat)<time())
            {
            $dataList[$rowCount] = array(
                'urun_baslik'         => $urunler['urun_baslik'],
				'urun_fiyat'          => $urunler['urun_fiyat'],
                'urun_resim'          => $urunler['urun_resim'],
                'urun_tarih'          => $urunler['urun_tarih'],
                'urun_okunma'         => $urunler['urun_okunma'],
                'kategori_id'           => $urunler['kategori_id'],
                'urun_id'             => $urunler['urun_id'],
                'urun_seo_etiket'     => $urunler['urun_seo_etiket'],
                'urun_seo_aciklama'   => $urunler['urun_seo_aciklama'],
                'urun_text'           => $urunler['urun_text'],
                'anasayfa_resim'      => $urunler['anasayfa_resim'],
                'urun_seflink'        => $urunler['urun_seflink'],
                'fiyat_goster'        => $urunler['fiyat_goster'],
                'aktifkontrol'        => $urunler['aktifkontrol'],
            );
            $rowCount++;
        }}
        return $dataList;
    }




	   public function tumurunlerilistele(){
        global $db;
        $dataList = array();
        $rowCount = 0;

       // $homepagesetting = $this->getHomepageSetting();
        $orderby = $this->getAyarlar('anasayfa_urunler', 'siralama');

        $tumurunlerilistele = $db->select('urunler')
            ->orderby('sira', $orderby)
            ->run();
        foreach($tumurunlerilistele as $urunler){
          $tarih = $urunler['tarih_zamanlama'];
          $saat = $urunler['saat_zamanlama'];
          if(strtotime($tarih." ".$saat)<time())
            {
            $dataList[$rowCount] = array(
                'urun_baslik'         => $urunler['urun_baslik'],
				'urun_fiyat'          => $urunler['urun_fiyat'],
                'urun_resim'          => $urunler['urun_resim'],
                'urun_tarih'          => $urunler['urun_tarih'],
                'urun_okunma'         => $urunler['urun_okunma'],
                'kategori_id'           => $urunler['kategori_id'],
                'urun_id'             => $urunler['urun_id'],
                'urun_seo_etiket'     => $urunler['urun_seo_etiket'],
                'urun_seo_aciklama'   => $urunler['urun_seo_aciklama'],
                'urun_text'           => $urunler['urun_text'],
                'anasayfa_resim'      => $urunler['anasayfa_resim'],
                'urun_seflink'        => $urunler['urun_seflink'],
                'fiyat_goster'        => $urunler['fiyat_goster'],
                'aktifkontrol'        => $urunler['aktifkontrol'],
            );
            $rowCount++;
        }}
        return $dataList;
    }



	/*
     * Eklenen Yeni (Anasayfa) Ürün Listesi*/
    public function urun_anasayfa_listele(){
        global $db;
        $dataList = array();
        $rowCount = 0;
        $urun_listele = $db->select('urunler')
		->LIMIT(0,6)
            ->orderby('urun_id','DESC')
            ->run();
        foreach($urun_listele as $urunler){
            $dataList[$rowCount] = array(
                'urun_baslik'         => $urunler['urun_baslik'],
				'urun_fiyat'          => $urunler['urun_fiyat'],
                'urun_resim'          => $urunler['urun_resim'],
                'urun_tarih'          => $urunler['urun_tarih'],
                'urun_okunma'         => $urunler['urun_okunma'],
                'kategori_id'           => $urunler['kategori_id'],
                'urun_id'             => $urunler['urun_id'],
                'urun_seo_etiket'     => $urunler['urun_seo_etiket'],
                'urun_seo_aciklama'   => $urunler['urun_seo_aciklama'],
                'urun_text'           => $urunler['urun_text'],
                'urun_seflink'        => $urunler['urun_seflink'],
            );
            $rowCount++;
        }
        return $dataList;
    }


    public function getHomepageSetting()
    {
        global $db;
        $setting = $db->select('modul_ayarlari')
            ->run();

        $orderby = 'ASC';
        $limit = 20;

        if ($setting[0]['listelemeturu'] != ''){
            $orderby = $setting[0]['listelemeturu'];
        }
        if ($setting[0]['listelemesayisi'] != ''){
            $limit = $setting[0]['listelemesayisi'];
        }
        $data = ['orderby'=>$orderby, 'limit'=>$limit];
        return $data;

    }

    public function getAnasayfaProje()
    {
        global $db;
        //$homepagesetting = $this->getHomepageSetting();
        $limit = $this->getAyarlar('anasayfa_projeler', 'listesayisi');
        $orderby = $this->getAyarlar('anasayfa_projeler', 'siralama');

        $projeler = $db->select('projeler')
            ->orderby('proje_sira', $orderby)
            ->limit(0, $limit)
            ->run();

        return $projeler;
    }
    public function getAnasayfaHizmet()
    {
        $durum = $this->modul_ayarlari('hizmetanasayfa');
        if ($durum=='aktif'){
            global $db;
            //$homepagesetting = $this->getHomepageSetting();

            $limit = $this->getAyarlar('anasayfa_hizmetler', 'listesayisi');
            $orderby = $this->getAyarlar('anasayfa_hizmetler', 'siralama');

            $hizmetler = $db->select('sayfalar')
                ->LIMIT(0,$limit)
                ->where('sayfa_type',3)
                ->orderby('sira', $orderby)
                ->run();

            return $hizmetler;
        } else {
            return false;
        }

    }


	    /* Eklenen Ürün Listesi*/
    public function benzerurunlistele(){ //Rasgele Ürün Detay Ürünleri
        global $db;
        $dataList = array();
        $rowCount = 0;
        $benzerurunlistele = $db->select('urunler')
            ->orderby('urun_id','DESC')
			      ->LIMIT(0,4)
            ->run();
        foreach($benzerurunlistele as $urunler){
            $dataList[$rowCount] = array(
                'urun_baslik'         => $urunler['urun_baslik'],
                'urunkodu'            => $urunler['urunkodu'],
				        'urun_fiyat'          => $urunler['urun_fiyat'],
                'urun_resim'          => $urunler['urun_resim'],
                'urun_tarih'          => $urunler['urun_tarih'],
                'urun_okunma'         => $urunler['urun_okunma'],
                'kategori_id'         => $urunler['kategori_id'],
                'urun_id'             => $urunler['urun_id'],
                'urun_seo_etiket'     => $urunler['urun_seo_etiket'],
                'urun_seo_aciklama'   => $urunler['urun_seo_aciklama'],
                'urun_text'           => $urunler['urun_text'],
                'anasayfa_resim'      => $urunler['anasayfa_resim'],
                'urun_seflink'        => $urunler['urun_seflink'],
                'fiyat_goster'        => $urunler['fiyat_goster'],
            );
            $rowCount++;
        }
        return $dataList;
    }

    public function benzerprojelistele(){
        global $db;
        $dataList = array();
        $rowCount = 0;
        $benzerurunlistele = $db->select('projeler')
            ->orderby('id','DESC')
            ->LIMIT(0,4)
            ->run();
        foreach($benzerurunlistele as $urunler){
            $dataList[$rowCount] = array(
                'proje_baslik'         => $urunler['proje_baslik'],
                'proje_fiyat'          => $urunler['proje_fiyat'],
                'proje_resim'          => $urunler['proje_resim'],
                'proje_tarih'          => $urunler['proje_tarih'],
                'proje_okunma'         => $urunler['proje_okunma'],
                'kategori_id'         => $urunler['proje_kategori'],
                'proje_id'             => $urunler['id'],
                'proje_seo_etiket'     => $urunler['proje_seo_etiket'],
                'proje_seo_aciklama'   => $urunler['proje_seo_aciklama'],
                'proje_text'           => $urunler['proje_text'],
                'anasayfa_resim'      => $urunler['proje_anaresim'],
                'proje_seflink'        => $urunler['proje_seflink'],
                'fiyat_goster'        => $urunler['proje_fiyatgoster'],
            );
            $rowCount++;
        }
        return $dataList;
    }




    /*
     * Eklenen Ürün Bilgileri
     * Değere Göre İşlem Yapılır*/
    public function urun($gelen,$par){
        global $db;
        $urun = $db->select('urunler')
            ->where('urun_seflink',$gelen)
            ->run(true);
        return $urun[$par];
    }

    /*
     * İletişime Geç Fonksiyonu*/
    public function iletisim($iletisim_adsoyad,$iletisim_telefon,$iletisim_email,$iletisim_konu,$iletisim_text){
        global $db;
        $iletisim = $db->insert('iletisim')
            ->set(array(
                'iletisim_adsoyad' => $iletisim_adsoyad,
                'iletisim_telefon' => $iletisim_telefon,
                'iletisim_email'   => $iletisim_email,
                'iletisim_konu'    => $iletisim_konu,
                'iletisim_text'    => $iletisim_text,
                'iletisim_tarih'   => date('d/m/Y')
            ));
        if($iletisim){
          echo "<div>
              <script>alert('Tebrikler! Mesajınız Bize Ulaştı, En Kısa Sürede Size Geri Dönüş Yapılacaktır.')</script>
              <meta http-equiv='refresh' content='0; URL=javascript:history.back(-1)'></div>";
        }else{
            echo '<div>
                Hata! Lütfen Daha Sonra Tekrar Deneyiniz.
            </div>';
        }
    }

    public function talepteklif($teklif_adsoyad, $teklif_telefon, $teklif_email, $talep_adres, $teklif_konu, $talep_hizmettalep, $teklif_text){
        global $db;
        $teklif = $db->insert('teklifler')
            ->set(array(
                'adsoyad' => $teklif_adsoyad,
                'telefon' => $teklif_telefon,
                'email'   => $teklif_email,
                'adres' => $talep_adres,
                'konu'    => $teklif_konu,
                'hizmettalep' => $talep_hizmettalep,
                'teklif'    => $teklif_text,
                'tarih'   => date('d/m/Y')
            ));
        if($teklif){
            echo "<div>
              <script>alert('Tebrikler! Talep/Teklifiniz Bize Ulaştı, En Kısa Sürede Size Geri Dönüş Yapılacaktır.')</script>
              <meta http-equiv='refresh' content='0; URL=javascript:history.back(-1)'></div>";
        }else{
            echo '<div>
                Hata! Lütfen Daha Sonra Tekrar Deneyiniz.
            </div>';
        }
    }

    public function getSmsSetting($firma)
    {
        global $db;
        $setting = $db->select('sms_ayarlari')
            ->where('sms_firma', $firma)
            ->run();
        foreach ($setting as $item) {
            if($firma == $item['sms_firma']){
                return $item;
            }
        }
        return $setting;
    }

    public function sendSMS($data)
    {
        $sms_modul = $this->modul_ayarlari('smsModulu');

        if ($sms_modul == 'aktif' ){
            $siteSettings = $this->getSiteSettings();
            $sms_firma = $this->getSiteSetting($siteSettings, 'sms_firma');
            $sms_yonetici_onay = $this->getSiteSetting($siteSettings, 'sms_yonetici_onay');
            $sms_yonetici_telefon = $this->getSiteSetting($siteSettings, 'sms_yonetici_telefon');
            $sms_yonetici_mesaj = $this->getSiteSetting($siteSettings, 'sms_yonetici_mesaj');
            $sms_ziyaretci_onay = $this->getSiteSetting($siteSettings, 'sms_ziyaretci_onay');
            $sms_ziyaretci_mesaj = $this->getSiteSetting($siteSettings, 'sms_ziyaretci_mesaj');

            if (!empty($sms_firma) && $sms_firma != '0'){
                $smsSettings = $this->getSmsSetting($sms_firma);
            } else {
                return false;
            }

            //Netgsm için sms gönderme
            if ($sms_firma == 'netgsm'){
                $username = $smsSettings['sms_kullanici'];
                $password = $smsSettings['sms_sifre'];
                $title = $smsSettings['sms_baslik'];
                include 'wpanel/System/Class/moduls/sms/netgsmsms.php';
                $netgsm = new Netgsmsms($username, $password, $title);
                if ($sms_yonetici_onay == 1 && !empty($sms_yonetici_mesaj) && !empty($sms_yonetici_telefon)){
                    $data['message'] = $sms_yonetici_mesaj;
                    $message = $this->smsReplaceVariable($data);
                    $phones = explode(',', $sms_yonetici_telefon);
                    foreach ($phones as $phone) {
                        $sendSms = $netgsm->sendSMS($phone,$message);
                    }
                }
                if ($sms_ziyaretci_onay == 1 && !empty($sms_ziyaretci_mesaj)){
                    $data['message'] = $sms_ziyaretci_mesaj;
                    $message = $this->smsReplaceVariable($data);
                    $phone = $data['telefon'];
                    $sendSms = $netgsm->sendSMS($phone,$message);
                }
            }

            //sitemio için sms gönderme
            if ($sms_firma == 'sitemio'){
                $apikey = $smsSettings['sms_sifre'];
                $title = $smsSettings['sms_baslik'];
                include 'wpanel/System/Class/moduls/sms/sitemio/SitemioSMS.php';
                $sitemio = new SitemioSMS($apikey);

                if ($sms_yonetici_onay == 1 && !empty($sms_yonetici_mesaj) && !empty($sms_yonetici_telefon)){
                    $data['message'] = $sms_yonetici_mesaj;
                    $message = $this->smsReplaceVariable($data);
                    $phones = explode(',', $sms_yonetici_telefon);
                    foreach ($phones as $phone) {
                        $sendSms = $sitemio->Submit($title, $message, $phone);
                    }
                }

                if ($sms_ziyaretci_onay == 1 && !empty($sms_ziyaretci_mesaj)){
                    $data['message'] = $sms_ziyaretci_mesaj;
                    $message = $this->smsReplaceVariable($data);
                    $phone = $data['telefon'];
                    $sendSms = $sitemio->Submit($title, $message, $phone);
                }

            } else {
                return false;
            }
        }

        return true;
    }

    public function smsReplaceVariable($data)
    {
        $istenmeyen = array('[isim]', '[telefon]', '[eposta]', '[konu]','[mesaj]');
        $degisen    = array($data['isim'], $data['telefon'], $data['eposta'], $data['konu'], $data['mesaj']);
        $result      = str_replace($istenmeyen, $degisen,$data['message']);
        return $result;
    }


    /* Siarpis Geç Fonksiyonu */
    public function siparisler($siparisler_adsoyad,$siparisler_telefon,$siparisler_email,$siparisler_konu,$siparisler_text,$siparisler_adres){
        global $db;
        $siparisler = $db->insert('siparisler')
            ->set(array(
                'siparisler_adsoyad' => $siparisler_adsoyad,
                'siparisler_email'   => $siparisler_email,
                'siparisler_konu'    => $siparisler_konu,
                'siparisler_text'    => $siparisler_text,
                'siparisler_telefon'  => $siparisler_telefon,
                'siparisler_adres'    => $siparisler_adres,
                'siparisler_tarih'   => date('d/m/Y')
            ));
        if($siparisler){
            echo "<div>
                <script>alert('Tebrikler! Siparişiniz Bize Ulaştı, En Kısa Sürede Size Geri Dönüş Yapılacaktır.')</script>
                <meta http-equiv='refresh' content='0; URL=javascript:history.back(-1)'></div>";
        }else{
            echo '<div>
                Hata! Lütfen Daha Sonra Tekrar Deneyiniz.
            </div>';
        }
    }

    /*
     * Foto Galeri Listeleme Fonksiyonu*/
    public function galeri(){
        global $db;
        $dataList = array();
        $rowCount = 0;
        $galeri_listele = $db->select('galeri')
            ->where('aktifkontrol','aktif')
        ->orderby('sira','ASC')
            ->run();
        foreach($galeri_listele as $galeri){
            $dataList[$rowCount] = array('galeriBaslik' => $galeri['galeriBaslik'],'galeriResim' => $galeri['galeriResim'],'aktifkontrol' => $galeri['aktifkontrol'], 'kategori'=> $galeri['kategori']);
            $rowCount++;
        }
        return $dataList;
    }

	    /*
     * Foto markalar Listeleme Fonksiyonu*/
    public function markalar(){
        global $db;
        $dataList = array();
        $rowCount = 0;
        //$homepagesetting = $this->getHomepageSetting();
        $limit = $this->getAyarlar('anasayfa_markalar', 'listesayisi');
        $orderby = $this->getAyarlar('anasayfa_markalar', 'siralama');
        $markalar_listele = $db->select('markalar')
        ->orderby('id','ASC')
            ->limit(0,$limit)
            ->run();
        foreach($markalar_listele as $markalar){
            $dataList[$rowCount] = array('markalarBaslik' => $markalar['markalarBaslik'],'markalarResim' => $markalar['markalarResim']);
            $rowCount++;
        }
        return $dataList;
    }


	    /*
     * Foto referans Listeleme Fonksiyonu*/
    public function referans(){
        global $db;
        $dataList = array();
        $rowCount = 0;
        $referans_listele = $db->select('referans')
            ->where('aktifkontrol', 'aktif')
            ->orderby('sira','ASC')
            ->run();
        foreach($referans_listele as $referans){
            $dataList[$rowCount] = array('referansBaslik' => $referans['referansBaslik'],'referansURL' => $referans['referansURL'],'referansResim' => $referans['referansResim'],'aktifkontrol' => $referans['aktifkontrol'], 'kategori'=>$referans['kategori']);
            $rowCount++;
        }
        return $dataList;
    }

    public function getKategori($sayfa_type){
        global $db;
        $kat_listele = $db->select('sayfalar')
            ->where('sayfa_type',$sayfa_type)
            ->run();
        return $kat_listele;
    }


    /*
     * Video Listeleme Fonksiyonu*/
    public function video(){
        global $db;
        $dataList = array();
        $rowCount = 0;
        $video_listele = $db->select('video')
        ->orderby('sira','ASC')
            ->run();
        foreach($video_listele as $video){
            $dataList[$rowCount] = array('id' => $video['id'],'videoBaslik' => $video['videoBaslik'],'videoResim' => $video['videoResim'],'videoSeflink' => $video['videoSeflink'],'videoEmbed' => $video['videoEmbed'],'videoText' => $video['videoText'],'aktifkontrol' => $video['aktifkontrol']);
            $rowCount++;
        }
        return $dataList;
    }


    /*
     * personel Listeleme Fonksiyonu*/
    public function personel(){
        global $db;
        $dataList = array();
        $rowCount = 0;
        $personel_listele = $db->select('personel')
        ->orderby('sira','asc')
            ->run();
        foreach($personel_listele as $personel){
            $dataList[$rowCount] = array('personelBaslik' => $personel['personelBaslik'],'personelResim' => $personel['personelResim'],'personelSeflink' => $personel['personelSeflink'],'personelEmbed' => $personel['personelEmbed'],'personelText' => $personel['personelText'],'aktifkontrol' => $personel['aktifkontrol']);
            $rowCount++;
        }
        return $dataList;
    }

    public function katalog()
    {
        global $db;
        $dataList = array();
        $rowCount = 0;
        $katalog_listele = $db->select('ekatalog')
            ->orderby('sira','asc')
            ->run();
        foreach ($katalog_listele as $katalog) {
            $dataList[$rowCount] = array('katalogBaslik'=>$katalog['isim'], 'katalogResim'=>$katalog['resim'], 'katalogPDF'=>$katalog['pdf'], 'katalogKategori'=>$katalog['kategori'], 'aktifkontrol' => $katalog['aktifkontrol']);
            $rowCount++;
        }
        return $dataList;
    }

    /*
     * Slider Resimleri Listeleme Fonksiyonu*/
    public function slider(){
        global $db;
        $dataList = array();
        $rowCount = 0;
        $slider_listele = $db->select('slider')
            ->orderby('sira','asc')
            ->run();

                  foreach($slider_listele as $slider){
                    $tarih = $slider['tarih_zamanlama'];
                    $saat = $slider['saat_zamanlama'];
                    if(strtotime($tarih." ".$saat)<time())
                      {
                      $dataList[$rowCount] = array(
                        'sliderBaslik' => $slider['sliderBaslik'],
                        'sliderResim' => $slider['sliderResim'],
                        'sira' => $slider['sira'],
                        'baslik_goster' => $slider['baslik_goster'],
                        'sliderText' => $slider['sliderText'],
                        'resim_goster' => $slider['resim_goster'] );
                      $rowCount++;
                  }}

        return $dataList;

    }

		    /*
     * hizmet Ürünlerine Ulaşma*/
    public function hizmetdetay($gelen,$par){
        global $db;
        $hizmetdetay = $db->select('sayfalar')
            ->where('sayfa_seflink',$gelen)
            ->run(true);
        return $hizmetdetay[$par];
    }

    public function tekilip($ip)
    {
      global $db;
      $ip_varmi = $db->select('tekil_sayac')
          ->where('ip',$ip)
          ->where('tarih',tarih(0,0,0,"Y-m-d"))
          ->from('count(id) as total')
          ->total();
      if($ip_varmi>0)
      {
      }
      else {
        $tekilsayac = $db->insert('tekil_sayac')
        ->set(array(
            'ip'    => $ip,
            'tarih'    => tarih(0,0,0,"Y-m-d")
          ));
      }
    }

    function getInstagramPosts()
    {
        global $db;
        $settings = $db->select('site_ayarlari')
            ->run();
        $instagram_client_id ='';
        $instagram_client_secret ='';
        $instagram_redirect_url ='';
        $instagram_access_token ='';
        foreach ($settings as $setting) {
            if ($setting['ayarAdi'] == 'instagram_client_id'){
                $instagram_client_id = $setting['ayarText'];
            }
            if ($setting['ayarAdi'] == 'instagram_client_secret'){
                $instagram_client_secret = $setting['ayarText'];
            }
            if ($setting['ayarAdi'] == 'instagram_redirect_url'){
                $instagram_redirect_url = $setting['ayarText'];
            }
            if ($setting['ayarAdi'] == 'instagram_access_token'){
                $instagram_access_token = $setting['ayarText'];
            }
        }
        $posts = $this->getpostCurl($instagram_access_token);
        return $posts;
    }


    function getpostCurl($access_token) {
        $url = 'https://api.instagram.com/v1/users/self/media/recent?access_token=' . $access_token;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $data = json_decode(curl_exec($ch), true);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $data;
    }





function adminyetki($admin_id,$par)
{
  global $db;
  $kullanici = $db->select('admin')
      ->where('admin_id',$admin_id)
      ->run(true);
  return $kullanici[$par];
}



}
// Lisanslama Sistemi
?>
