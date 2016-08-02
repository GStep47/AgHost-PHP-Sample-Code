<?php

//This code manages DTN Weather API tokens.
//Each DTN Weather API token is good for 30 minutes from creation.
//This code checks existing file tkn.php to see if a valid token is stored there.
//If not, it retrieves a new token from the DTN Weather API and stores it there.

if (!file_exists("tkn.php") OR (time()-filemtime("tkn.php")) > 800) {
		//if TKN.PHP does not exist or is more than 800 seconds old, generate new token

		//build URL 
		$username = ""; //your Weather API username
		$password = ""; //your Weather API password
		$theurl = 'http://api.aghost.net/api/weather/?method=getToken&username=' . $username . '&password=' . $password;
		
		//cURL session to retrieve XML data
		$handle = curl_init();

		//cURL parameters
		curl_setopt($handle, CURLOPT_URL, $theurl);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 0); //do not verify certificate
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1); //return the material instead of 'OK'
		curl_setopt($handle, CURLOPT_HTTPGET, 1); //Force HTTP-GET; POST will also work

		//execute
		$results = curl_exec ($handle);
		curl_close ($handle);

		//Load retrieved data into variable
		$xml = simplexml_load_string($results) or die('failed on simplexml_Load_string loading retrieved data');
		$result = $xml->xpath('/tns:RequestAndResponse/response/tns:AccountToken/URLEncodedToken');
		$token = $result[0];
		
		//encrypt token for storage
		$ciphertext = openssl_encrypt($token, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
		
		//write token to file
		unlink("tkn.php"); //delete existing tkn.php file
		$thefile = fopen("tkn.php", "w") or die("<br />Unable to open token file!"); //create file
		fwrite($thefile, $ciphertext); //write token to file
		//fwrite($thefile, $token); //write token to file
		fclose($thefile); //close

} else { //read existing token from file
		$thefile = fopen("tkn.php", "r") or die("<br />Unable to open token file!"); //open file
    	$ciphertext = file_get_contents("tkn.php"); //read file
		fclose($thefile); //close
		
		//decrypt ciphertext
		$token = openssl_decrypt($ciphertext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
}

?>
