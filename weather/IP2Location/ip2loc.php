<html>
<head></head>
<body>
<!-- Style for weather component -->
<style type="text/css">
.dtn-weather {
    box-shadow: 0 1px 4px 0 rgba(0, 0, 0, 0.2);
    padding: 12px 0;
	width: 624px;
}
.dtn-forcast {
	width: 650px;
}
.dtn-forcast-single {
    box-shadow: 0 1px 4px 0 rgba(0, 0, 0, 0.2);
    float: left;
    margin: 0 10px 10px 0;
    padding: 0 0 0 6px;
	width: 17%;
}
.dtn-temp-digit {
    font-size: 108px;
    line-height: 100px;
    margin-left: 12px;
}
.dtn-temp-scale {
    font-size: 26px;
}
.dtn-temp div {
    float: left;
}
.dtn-conditions-left div {
    float: left;
    height: 96px;
}
.dtn-conditions-left img {
    float: left;
    margin-left: -5px;
}
.dtn-conditions-left {
    float: left;
}
.dtn-conditions-right {
    float: right;
    min-width: 50%;
    padding-top: 6px;
}
.dtn-conditions-desc {
    font-size: 18px;
}
.dtn-conditions-radar {
    float: right;
    min-width: 50%;
}
</style>

<h2>IP2LocTest for DTN AgHost Weather API</h2>
<h3>Overview</h3>
<p>This is a simple .PHP file demonstrating how the databases at <a href="ip2location.com">IP2LOCATION.com</a> can be used to determine a website visitor's location, and then use that location to retrieve local weather from the DTN AgHost Weather API.</p>
<p>The DTN AgHost Weather API can retrieve local weather maps and information based on a zip code, city/state pair, or latitude/longitude pair. (Canadian provinces and postal codes are also supported.) IP2Location.com offers various databases that contains all the sets of information.</p>
<p>This demonstration file uses the <a href="https://www.ip2location.com/samples/db3-ip-country-region-city.txt">sample version</a> of <a href="https://www.ip2location.com/databases/db3-ip-country-region-city">DB3, "IP-Country-Region-City Database"</a> which returns a city-state pair. I feel city/state pair is the best and most cost-effective choice for this purpose. Though the database and code could be adapted to use one of the zip code- or lat/lon-specific databases if desired.</p>
<h3>About the IP2Location.com database</h3>
<p>Full specifications for the ip-country-region-city database are <a href="https://www.ip2location.com/docs/db3-ip-country-region-city-specification.pdf">here</a>. This sample file runs against a MySQL database table based on this information.</p> 
<p>The IP2Location database includes full state names ("Nebraska") while the DTN AgHost Weather API requires the two-letter abbreviation ("NE"). To convert this, my database contains a second table <b>ip2locst</b> which converts the full state name to the code via a sub-query.</p>
<p>The sample database has a very limited IP range. This demonstration displays your IP address and queries the database for it. If your IP address is not in the demo range (which it is probably not), it generates a random IP address that is within the range and queries that. (This code could be adapted to query on a default city/state if the database lookup fails for whatever reason.)</p>
<hr />
<?php

$yourIPaddress = $_SERVER['REMOTE_ADDR'];

echo "<p><b>You are visiting from</b>: " . $yourIPaddress. "</p>";

//convert IP address to a number. That is how Ip2location database stores it.
//If the Address is A.B.C.D.
//The IP number X is X = A x (256*256*256) + B x (256*256) + C x 256 + D

//find positions of . in IP address
$position = array(3);
$position[0]=stripos($yourIPaddress, ".");
$position[1]=stripos($yourIPaddress, ".", $position[0]+1);
$position[2]=stripos($yourIPaddress, ".", $position[1]+1);

//create variables
$a = substr($yourIPaddress, 0, $position[0]);
$b = substr($yourIPaddress, $position[0]+1, $position[1]-$position[0]-1);
$c = substr($yourIPaddress, $position[1]+1, $position[2]-$position[1]-1);
$d = substr($yourIPaddress, $position[2]+1);

//do the math
$IPNumber = ($a*256*256*256) + ($b*256*256) + ($c*256) + $d;

//connect to your MySQL database - these credentials are for your MySQL database, not the DTN AgHost Weather API
$host = "";
$username = "";
$password = "";
$dbname = "";

$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

//create query
$query1 = "SELECT CITY, (SELECT CODE FROM ip2locst WHERE NAME=REGION) AS STATE FROM ip2loc WHERE COUNTRY_CODE IN (\"US\", \"CA\") AND (" . $IPNumber .  " BETWEEN ip2loc.IP_FROM AND ip2loc.IP_TO)=1";

if(!$result = $conn->query($query1)){
    die('There was an error running the query [' . $conn->error . ']');
}

//echo "<p>Total results: " . $result->num_rows . "</p>";
  
if ($result->num_rows == 0) {
	echo "<p>Your IP address is not in the database, so we will query a random location that is.</p>";
	$IPNumber = mt_rand(67260672, 67339519); //the range of IP numbers that is in the demo database
	//In a live environment, you would use this section to set a default location. I'm doing this to demonstrate that the DB lookup works.
	
	//run the query again with the random $IPNumber
	$query1 = "SELECT CITY, (SELECT CODE FROM ip2locst WHERE NAME=REGION) AS STATE FROM ip2loc WHERE COUNTRY_CODE IN (\"US\", \"CA\") AND (" . $IPNumber .  " BETWEEN ip2loc.IP_FROM AND ip2loc.IP_TO)=1";	
	echo "<p><b>Random IP address</b>: " . $IPNumber . "</p>";
	
	$result->free(); //clear result to re-run
	
	if(!$result = $conn->query($query1)){
		die('There was an error running the query [' . $conn->error . ']');
	}
}

//whichever you did, display the results
$row = $result->fetch_row();
$city = urlencode($row[0]);
$state = urlencode($row[1]);
echo "<p>Location is " . urldecode($city) . ", " . $state . "</p>";

//free result
$result->free();

// Get thread id and use it to kill database connection
$t_id=mysqli_thread_id($conn);
mysqli_kill($conn,$t_id);

//BEGIN WEATHER_GET_TOKEN.PHP SAMPLE CODE

//load URL into an object
$username = ""; //your username
$password = ""; //your password
$theurl = 'http://api.aghost.net/api/weather/?method=getToken&username=' . $username . '&password=' . $password;
$xmlfile = simplexml_load_file($theurl);

//pull desired element from object by giving it the XML structure,
//including namespaces where they exist, as an argument
$result = $xmlfile->xpath('/tns:RequestAndResponse/response/tns:AccountToken/URLEncodedToken');

//$result is an array of all XML children matching the xpath
$thetoken = $result[0];

//echo $thetoken;

//END WEATHER_GET_TOKEN.PHP SAMPLE CODE


//This code shows you how to retrieve a Weather XML file, extract one node of data, and print it to the screen.

echo "<br />";

//load URL into an object
//NOTE: $theurl may be altered to construct the call you wish to make. See Weather API manual for parameter options.
$theurl = 'http://api.aghost.net/api/weather/?method=getStationWeather&token=' . $thetoken . '&city=' . $city . '&state=' . $state . '&dailyfc=5&hrlyobs=1';

//When developing, it may be useful to dump $theurl to the screen, then enter that URL into a web browser.
//You can see the returned XML in the browser. This will ensure correct XML retrieval.

//load contents of XML file into object
$xml = simplexml_load_file($theurl);

//pull desired elements from object by giving it the XML structure,
//including namespaces where they exist, as an argument

//station data - note wildcard
$resultsd = $xml->xpath('/tns:RequestAndResponse/response/tns:StationWeather/Station/*'); 

//observations - note wildcard and descendant::
$resultobs = $xml->xpath('/tns:RequestAndResponse/response/tns:StationWeather/HourlyObservationData/HourlyObservation/descendant::*');

//forecast - // note wildcard and descendant::
$resultfc = $xml->xpath('/tns:RequestAndResponse/response/tns:StationWeather/DailyForecastData/DailyForecast[1]/descendant::*'); 

//In this example, we are retrieving data for the weather station. You can alter the XML path as desired.
//XML schema: http://api.aghost.net/api/weather/weatherService.xsd

//print each item of the array to the screen, with appropriate HTML:
echo "<h3>Weather Station Info</h3>";
echo "<table border=1>";
echo "<tr><td>Weather Station Identifier: </td><td>" . $resultsd[0] . "</td></tr>";
echo "<tr><td>Weather Station Latitude/Longitude: </td><td>" . $resultsd[1] . ", " . $resultsd[2] . "</td></tr>";
echo "<tr><td>Weather Station Location: </td><td>" . $resultsd[3] . ", " . $resultsd[4] . ", " . $resultsd[5] . "</td></tr>";
echo "</table>";

//current conditions
echo "<h3>Current Conditions</h3>";
echo"\n" . '		<div class="dtn-weather">';
echo"\n" . '			<div>';
echo"\n" . '				<div class="dtn-temp">';
echo"\n" . '					<div class="dtn-temp-digit">' . round($resultobs[5],0) . '</div>';
echo"\n" . '					<div class="dtn-temp-scale">&deg;F</div>';
echo"\n" . '				</div>';
echo"\n" . '				<div class="dtn-conditions-right">';
echo"\n" . '					<div class="dtn-conditions-desc">';
echo"\n" . '						Wind: ' . $resultobs[11] . ' at ' . round($resultobs[13],0) . ' MPH';
echo"\n" . '						<br>';
echo"\n" . '						Humidity: ' . $resultobs[8] . ' %';
echo"\n" . '						<br>';
echo"\n" . '						Soil Temperature: ' . round($resultobs[15],0) . '&deg; F';
echo"\n" . '					</div>';
echo"\n" . '				</div>		';
echo"\n" . '			</div>';
echo"\n" . '			';
echo"\n" . '			<div style="clear:both"></div>';
echo"\n" . '			';
echo"\n" . '			<div>';
echo"\n" . '				<div class="dtn-conditions-left">';
echo"\n" . '						<img src="img/wxiconsm/' . $resultobs[4] . '.png">';
echo"\n" . '						<div class="dtn-conditions-desc">';
echo $result[2];
echo"\n" . '							<br>';
echo $result[3];
echo"\n" . '							<br>';
echo"\n" . '							Feels like ' . round($resultobs[6],0) . '&deg; F								';
echo"\n" . '						</div>	';
echo"\n" . '				</div>';
echo"\n" . '				<div class="dtn-conditions-radar">';
echo"\n" . '						';
echo"\n" . '						<img src="https://agwx.dtn.com/RegionalRadar.cfm?city=' . $city . '&state=' . $state . '&key=' . $thetoken . '&level=3&animate=1&width=400&height=250">';
echo"\n" . '						';
echo"\n" . '						';
echo"\n" . '						';
echo"\n" . '				</div>';
echo"\n" . '			</div>';
echo"\n" . '			';
echo"\n" . '			<div style="clear:both"></div>';
echo"\n" . '			';
echo"\n" . '		</div>';

//5-day forecast
//I really need to put this in a for-next loop. However, you can't iterate a relative URL this way
echo"\n" . '	<h4>5-day Forecast</h4>';
echo"\n" . '	<div class="dtn-forcast">';
echo"\n" . '		<div class="dtn-forcast-single">';
echo"\n" . '			<p>';
echo date('D', strtotime($resultfc[2]));
echo"\n" . '<br>';
echo"\n" . '			<img src="img/wxicons/' . $resultfc[5] . '.png">';
echo"\n" . '			<br>';
echo $resultfc[4];
echo"\n" . '			<br>';
echo"\n" . '			High ' . $resultfc[6] . '&deg; F';
echo"\n" . '			<br>';
echo"\n" . '			Low ' . $resultfc[7] . '&deg; F';
echo"\n" . '			<br>';
echo"\n" . '			Chance of';
echo"\n" . '			<br>';
echo"\n" . '			Precip ' . $resultfc[11]. '%';
echo"\n" . '			<br>';
echo"\n" . '			</p>';
echo"\n" . '		</div>';
echo"\n" . '		';
echo"\n" . '		';
echo"\n" . '		<div class="dtn-forcast-single">';
$resultfc = $xml->xpath('/tns:RequestAndResponse/response/tns:StationWeather/DailyForecastData/DailyForecast[2]/descendant::*'); //note wildcard
echo"\n" . '			<p>';
echo date('D', strtotime($resultfc[2]));
echo"\n" . '<br>';
echo"\n" . '			<img src="img/wxicons/' . $resultfc[5] . '.png">';
echo"\n" . '			<br>';
echo $resultfc[4];
echo"\n" . '			<br>';
echo"\n" . '			High ' . $resultfc[6] . '&deg; F';
echo"\n" . '			<br>';
echo"\n" . '			Low ' . $resultfc[7] . '&deg; F';
echo"\n" . '			<br>';
echo"\n" . '			Chance of';
echo"\n" . '			<br>';
echo"\n" . '			Precip ' . $resultfc[11]. '%';
echo"\n" . '			<br>';
echo"\n" . '			</p>';
echo"\n" . '		</div>';
echo"\n" . '		';
echo"\n" . '		';
echo"\n" . '		<div class="dtn-forcast-single">';
$resultfc = $xml->xpath('/tns:RequestAndResponse/response/tns:StationWeather/DailyForecastData/DailyForecast[3]/descendant::*'); //note wildcard
echo"\n" . '			<p>';
echo date('D', strtotime($resultfc[2]));
echo"\n" . '<br>';
echo"\n" . '			<img src="img/wxicons/' . $resultfc[5] . '.png">';
echo"\n" . '			<br>';
echo $resultfc[4];
echo"\n" . '			<br>';
echo"\n" . '			High ' . $resultfc[6] . '&deg; F';
echo"\n" . '			<br>';
echo"\n" . '			Low ' . $resultfc[7] . '&deg; F';
echo"\n" . '			<br>';
echo"\n" . '			Chance of';
echo"\n" . '			<br>';
echo"\n" . '			Precip ' . $resultfc[11]. '%';
echo"\n" . '			<br>';
echo"\n" . '			</p>';
echo"\n" . '		</div>';
echo"\n" . '		';
echo"\n" . '		';
echo"\n" . '		<div class="dtn-forcast-single">';
$resultfc = $xml->xpath('/tns:RequestAndResponse/response/tns:StationWeather/DailyForecastData/DailyForecast[4]/descendant::*'); //note wildcard
echo"\n" . '			<p>';
echo date('D', strtotime($resultfc[2]));
echo"\n" . '<br>';
echo"\n" . '			<img src="img/wxicons/' . $resultfc[5] . '.png">';
echo"\n" . '			<br>';
echo $resultfc[4];
echo"\n" . '			<br>';
echo"\n" . '			High ' . $resultfc[6] . '&deg; F';
echo"\n" . '			<br>';
echo"\n" . '			Low ' . $resultfc[7] . '&deg; F';
echo"\n" . '			<br>';
echo"\n" . '			Chance of';
echo"\n" . '			<br>';
echo"\n" . '			Precip ' . $resultfc[11]. '%';
echo"\n" . '			<br>';
echo"\n" . '			</p>';
echo"\n" . '		</div>';
echo"\n" . '		';
echo"\n" . '		';
echo"\n" . '		<div class="dtn-forcast-single">';
$resultfc = $xml->xpath('/tns:RequestAndResponse/response/tns:StationWeather/DailyForecastData/DailyForecast[5]/descendant::*'); //note wildcard
echo"\n" . '			<p>';
echo date('D', strtotime($resultfc[2]));
echo"\n" . '<br>';
echo"\n" . '			<img src="img/wxicons/' . $resultfc[5] . '.png">';
echo"\n" . '			<br>';
echo $resultfc[4];
echo"\n" . '			<br>';
echo"\n" . '			High ' . $resultfc[6] . '&deg; F';
echo"\n" . '			<br>';
echo"\n" . '			Low ' . $resultfc[7] . '&deg; F';
echo"\n" . '			<br>';
echo"\n" . '			Chance of';
echo"\n" . '			<br>';
echo"\n" . '			Precip ' . $resultfc[11]. '%';
echo"\n" . '			<br>';
echo"\n" . '			</p>';
echo"\n" . '		</div>';
echo"\n" . '		';
echo"\n" . '	</div>';
?>

</body>
</html>
