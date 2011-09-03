<?php
/* 
 * BMKG Library versi 0.1.10 https://github.com/dimassony/BMKG-Library
 * PHP Library untuk membaca API BMKG (http://bmkg.go.id)
 * Dimas Ahmad Eka Putra | dimas@dimassony.com | http://dimassony.com
 */

/* BMKG Library class */
class BMKG {
	// Versi ini.
	public $VERSI = 000110;
	public $VERSI_BERTITIK = '0.1.10';
	// Tentukan alamat utama API.
	public $host = 'http://data.bmkg.go.id/';
	// Atur timeout standar.
	public $timeout = 300; // detik
	// Atur connecttimeout standar.
	public $connecttimeout = 300; // detik
	// Atur useragent.
	public $useragent = 'BMKG Library v0.1.10';
	// Informasi eksekusi. Berguna untuk mencari bug atau sekedar mengakses informasi eksekusi.
	public $info = array();
	// Simpan Objek SimpleXML di sini.
	public $simplexmlobject;
	
	/* Buka data dari BMKG */
	function __construct($url, $pakaisimplexml = true){
		// Pastikan hanya membuka file berekstensi .xml
		if(strrpos($url, '.xml')){
			$url = $this->host . $url;
			// Buka data
			$xml = $this->akseshttp($url);
			// Periksa apakah menggunakan DOMXPath atau masih dalam format XML
			if($pakaisimplexml)
				// Berikan data menjadi objek SimpleXML
				$this->simplexmlobject = new SimpleXMLElement($xml);
			else
				// Berikan dalam format xml
				return $xml;
		} else {
			die('KESALAHAN! Pastikan url yang anda masukkan berekstensi .xml!');
		}
	}
	
	/* Ubah array dan kembalikan menjadi objek SimpleXML */
	function xpath($xpath){
		$simplexml = $this->simplexmlobject;
		$array = $simplexml->xpath($xpath);
		return $array[0];
	}
	
	/* Buat panggilan ke server.    *
	 * return: hasil panggilan API. */
	function akseshttp($url){
		// Periksa ekstensi curl, pakai file_get_contents sebagai alternatif.
		if (extension_loaded('curl')) {
			// Mulai
			$a = curl_init();
			// Atur opsi http
			curl_setopt($a, CURLOPT_USERAGENT, $this->useragent);
			curl_setopt($a, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
			curl_setopt($a, CURLOPT_TIMEOUT, $this->timeout);
			curl_setopt($a, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($a, CURLOPT_HEADER, FALSE);
			curl_setopt($a, CURLOPT_URL, $url);
			// Eksekusi
			$respon = curl_exec($a);
			// Ambil informasi tambahan
			$this->info['akseshttp_metode'] = 'cURL';
			$this->info['http_kode'] = curl_getinfo($a, CURLINFO_HTTP_CODE);
			$this->info['http_header'] = curl_getinfo($a);
			$this->info['url'] = $url;
			
			// Tutup
			curl_close($a);
		} else {
			// Atur opsi http
			$konteks = stream_context_create(array('http' => array(
				'timeout' => $this->timeout,
				'user_agent' => $this->useragent
			)));
			// Eksekusi
			$respon = file_get_contents($url, false, $konteks);
			// Ambil informasi tambahan
			$this->info['akseshttp_metode'] = 'file_get_contents';
			$this->info['http_header'] = $http_response_header;
			$this->info['url'] = $url;
		}
		// Berikan hasil respon
		return $respon;
	}
}
?>