DTN AgHost Weather Readme

The DTN AgHost Weather API allows you to retrieve weather data via XML, and JPG images of weather maps.

The system requires you to request a token, and then use that token to request the actual data and images. I have also written code that stores the token (in an encrypted form), and re-uses it, or retrieves a new token when needed. 

dtn-weather-get-token.php either retrieves the token from the file where it is stored (tkn.php), or generates a new one and stores it in that file.
