<?php

$enteredzip=$_POST['userzip']; 

//sanitize entered data - remove everything except digits
$enteredzip = filter_var($enteredzip,FILTER_SANITIZE_NUMBER_INT); 

//sanitize entered data - if zip code is not long enough, set it to the default 99163
if (strlen($enteredzip) <5) {
	$enteredzip = "99163";
}

//set cookie
setcookie ("WeatherZip", $enteredzip, time()+120000000, "/"); //about 3.8 years

//redirect to index.php
header("location:index.php");

?>
