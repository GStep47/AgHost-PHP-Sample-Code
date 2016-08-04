<b>DTN AgHost Weather Content PHP Code</b>

This is PHP code supporting the DTN AgHost Weather API. The DTN AgHost Weather API allows you to retrieve weather data via XML, and JPG images of weather maps. You must have a subscription to their service.

To request maps or information, you must first request a token, and then use that token to request the actual data and images. Tokens expire after 30 minutes.

<b>dtn-weather-get-token.php</b> manages the token. When the token is needed, this code either retrieves the token from the local file where it is stored (tkn.php), or generates a new token and stores it in that file for later use. The token is store in an encrypted form.

<b>dtn-weather-set-cookie.php</b> sanitizes the zip code entered by the user, sets a cookie in their browser, and redirects them to the weather page with the zip code provided.

<b>dtn-weather.php</b> is the  block of weather content itself. It is meant to be included into a larger page, at the point where you want the weather to appear. The HTML can be edited as you wish.

<b>index.php</b> is a basic page or lorem ipsum text. It is meant to illustrate how the weather block can be inserted and flow around other content.

DEPENDENCIES
dtn-weather.php requests .PNG files stored in subfolders /img/wxicons and /img/wxiconsm. These can be downloaded from agdocs.dtn.com if you have an account there.
