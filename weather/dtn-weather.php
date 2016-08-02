
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
			
<?php	

//NOTE: $token is determined by dtn-weather-get-token.php, which must be included before this file.

			if ($_COOKIE["WeatherZip"]) {
				$zip = $_COOKIE["WeatherZip"];
			} else {
				$zip = 99163; //default zip - Four Star's address in Pullman WA
			}

$theurl = 'http://api.aghost.net/api/weather/?method=getStationWeather&token=' . $token . '&zip=' . $zip . '&dailyfc=5&hrlyobs=1';

//cURL session to retrieve XML data
$handle = curl_init();

//cURL parameters
curl_setopt($handle, CURLOPT_URL, $theurl);
curl_setopt($handle, CURLOPT_POSTFIELDS, $postfields);
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 0); //do not verify certificate
curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1); //return the material instead of 'OK'
curl_setopt($handle, CURLOPT_HTTPGET, 1); //Force HTTP-GET; post will also work

//execute
$results = curl_exec ($handle);
curl_close ($handle);

//Load retrieved data into variable
$xml = simplexml_load_string($results) or die('failed on simplexml_Load_string in dtn-weather.php');

$result = $xml->xpath('/tns:RequestAndResponse/response/tns:StationWeather/HourlyObservationData/HourlyObservation/descendant::*'); 
//note wildcard and descendant::
//$result is an array of all XML children matching the above XML path

//current conditions
echo"\n" . '		<div class="dtn-weather">';
echo"\n" . '			<div>';
echo"\n" . '				<div class="dtn-temp">';
echo"\n" . '					<div class="dtn-temp-digit">' . round($result[5],0) . '</div>';
echo"\n" . '					<div class="dtn-temp-scale">&deg;F</div>';
echo"\n" . '				</div>';
echo"\n" . '				<div class="dtn-conditions-right">';
echo"\n" . '					<div class="dtn-conditions-desc">';
echo"\n" . '						Wind: ' . $result[11] . ' at ' . round($result[13],0) . ' MPH';
echo"\n" . '						<br>';
echo"\n" . '						Humidity: ' . $result[8] . ' %';
echo"\n" . '						<br>';
echo"\n" . '						Soil Temperature: ' . round($result[15],0) . '&deg; F';
echo"\n" . '					</div>';
echo"\n" . '				</div>		';
echo"\n" . '			</div>';
echo"\n" . '			';
echo"\n" . '			<div style="clear:both"></div>';
echo"\n" . '			';
echo"\n" . '			<div>';
echo"\n" . '				<div class="dtn-conditions-left">';
echo"\n" . '						<img src="/img/wxiconsm/' . $result[4] . '.png">';
echo"\n" . '						<div class="dtn-conditions-desc">';
echo $result[2];
echo"\n" . '							<br>';
echo $result[3];
echo"\n" . '							<br>';
echo"\n" . '							Feels like ' . round($result[6],0) . '&deg; F								';
echo"\n" . '						</div>	';
echo"\n" . '				</div>';
echo"\n" . '				<div class="dtn-conditions-radar">';
echo"\n" . '						';
echo"\n" . '						<img src="https://agwx.dtn.com/RegionalRadar.cfm?zip=' . $zip . '&key=' . $token . '&level=3&animate=1&width=400&height=250">';
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
$resultfc = $xml->xpath('/tns:RequestAndResponse/response/tns:StationWeather/DailyForecastData/DailyForecast[1]/descendant::*'); //note wildcard
echo"\n" . '			<p>';
echo date('D', strtotime($resultfc[2]));
echo"\n" . '<br>';
echo"\n" . '			<img src="/img/wxicons/' . $resultfc[5] . '.png">';
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
echo"\n" . '			<img src="/img/wxicons/' . $resultfc[5] . '.png">';
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
echo"\n" . '			<img src="/img/wxicons/' . $resultfc[5] . '.png">';
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
echo"\n" . '			<img src="/img/wxicons/' . $resultfc[5] . '.png">';
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
echo"\n" . '			<img src="/img/wxicons/' . $resultfc[5] . '.png">';
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
