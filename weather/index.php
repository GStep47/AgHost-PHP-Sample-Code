<html>
<head></head>
<body>
<h3>This is a very simple HTML page, which demonstrates inserting the component dtn-weather.php within it.</h3>

<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis at metus ornare, condimentum odio id, faucibus arcu. Curabitur malesuada lorem at dignissim varius. Integer mollis felis at rhoncus aliquam. Morbi felis mauris, convallis eget risus et, egestas maximus eros. Curabitur ultricies enim mauris, quis ullamcorper purus bibendum ac. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit tincidunt lacus. Praesent pharetra augue non orci bibendum tincidunt. Maecenas in laoreet risus. Aliquam eget congue urna, eu molestie turpis. Nulla ornare fermentum elit, id rhoncus quam pretium id. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec facilisis auctor ultricies.</p>

<p>Aliquam molestie tempus dapibus. Integer ac augue a massa laoreet commodo. Nullam malesuada tristique nunc nec tristique. Nam nisi enim, tempus non libero ornare, lobortis ultricies purus. Suspendisse potenti. Donec consectetur interdum ultricies. Suspendisse iaculis libero ac ante fringilla condimentum. Vestibulum eleifend sed lectus pharetra laoreet. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. In faucibus mauris vel libero luctus rhoncus.</p>

				<?php include("dtn-weather-get-token.php"); ?>
				<?php include("dtn-weather.php"); ?>


				<div class="zipcodeform">
				<form name="zipform" method="post" action="dtn-weather-set-cookie.php">
				<b>Zip Code:</b> <input name="userzip" type="text" id="userzip" size="6" maxlength="5">
				<input type="submit" name="Submit" value="Enter"></form>
				</div>

<p>Duis at quam porta, pellentesque quam vel, rutrum libero. Curabitur pulvinar quis nulla sit amet ultrices. Phasellus felis nisl, ornare eget neque a, iaculis mattis urna. Fusce ante leo, maximus non tortor sed, ultricies tempor dui. Mauris a dignissim lacus. Integer ultricies pharetra nibh, non dictum velit porttitor ac. Duis commodo vel nisl sit amet elementum. Aliquam erat volutpat. Cras a nibh tempor, euismod tortor sed, eleifend lorem. Phasellus pulvinar euismod lectus, tempor semper justo condimentum nec. Donec at diam ut enim vehicula facilisis. Nunc ullamcorper enim eu magna volutpat, elementum volutpat eros semper. </p>

</body>
</html>
