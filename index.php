<?php

//Work Completed by Jalaluddin Qureshi (Individually), for HTTP5203, API Project, Winter 2021, Humber College.

define('fetch_bearer', TRUE);

//the bearer.php file contains information about the bearer token, and has been hidden.
//$twitter_bearer = "Authorization: Bearer #####";
require_once('bearer.php');

$base_url = "https://api.twitter.com/2/tweets/search/recent?query=";

if(isset($_POST['searchTweet'])){
	
	$keyword = $_POST['keyword'];
	
	$search_msg = "Based on your search of the keyword of <strong>" . $keyword . "</strong>. The location of users who tweeted with this keywords are marked as follow.";
	
	//to the restrictions on the number of requests which can be made to geolocation, this seems to be a reasonable number.
	//https://developers.google.com/maps/documentation/geocoding/usage-and-billing
	$number_tweets = 25;
	
	$url = $base_url . $keyword . "&tweet.fields=geo,created_at&expansions=author_id&user.fields=location,name&max_results=" . $number_tweets;
	
	$header = array($twitter_bearer);
	
	$out = fopen("tweet_result.json","wb");
	
	//Example Curl: curl --location --request GET 'https://api.twitter.com/2/tweets/search/recent?query=nyc' -H "Authorization: Bearer BEARER-TOKEN"
	$opts = array(
		CURLOPT_HTTPHEADER => $header,
		CURLOPT_URL => $url,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_FILE => $out,
		CURLOPT_HTTPGET	=> true
	);
	
	
	$c = curl_init();
	curl_setopt_array($c, $opts);
	$result = curl_exec($c);
	curl_close($c);
	
	//to convert JSON to array.
	$string = file_get_contents("tweet_result.json");
	$json = json_decode($string, true);
	
	//generates an unordered list of location from user account.
	//also fetches array values to display Tweets.
	
	$dtz = new DateTimeZone("America/Toronto");	
	
	$tweets_display = "<h2 id=\"heading\">Tweets Matching the Search Query</h2>";
	$list_locations = "<ul>";
	for ($i=0; $i<count($json["includes"]["users"]); $i++) {
		
		$dt = new DateTime($json["data"][$i]["created_at"], $dtz);
		
		$tweets_display .= "<section class=\"message-div\"> <p class=\"message-head\">" . $json["includes"]["users"][$i]["username"] . " on " ;
		
		$tweets_display .= $dt->format("Y-m-d") . " (" . $dt->format("H:i:s") . ") wrote:</p> <p class=\"message\">" . 
							$json["data"][$i]["text"] . "</p></section>";
			
			//the following information is used for geocoding by the Javascript file.
			if (isset($json["includes"]["users"][$i]["location"])) {
				$list_locations .= "<li class='location'>" . $json["includes"]["users"][$i]["location"] . "</li>";
			}
		
	}
	$list_locations .= "</ul>";
	
}

//Create Data-pipeline. Add Step 7 to monitor live stream.
//This function was tested but not used, as the geo specifiec rules are enterprise grade.
//https://developer.twitter.com/en/docs/twitter-api/tweets/filtered-stream/quick-start
//https://developer.twitter.com/en/docs/twitter-api/tweets/search/integrate/build-a-query

function create_rule($bearer) {
	
	$url = "https://api.twitter.com/2/tweets/search/stream/rules";
	
	$header = array(
				"Content-type: application/json",
				$bearer);
	
	$data = '{"delete": {
		"ids": [
        "1388647785843601417"]}}';
	
	/*'{"add": [{"value": "covid #Canada", "tag": "Tweets from Canada"}]}';*/
	
	$postdata = json_encode($data);
	var_dump($postdata);
	
	/*Example Curl: 
	curl -X POST 'https://api.twitter.com/2/tweets/search/stream/rules' \
			-H "Content-type: application/json" \
			-H "Authorization: Bearer $BEARER_TOKEN" -d \
			'{
				"add": [{"value": "cat has:media", "tag": "cats with media"}]
			}'*/
	$opts = array(
		CURLOPT_HTTPHEADER => $header,
		CURLOPT_URL => $url,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => $data
	);
	
	
	$c = curl_init();
	curl_setopt_array($c, $opts);
	$result = json_decode(curl_exec($c));
	var_dump($result);
	curl_close($c);
	
}



?>



<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Google Maps Tweet Markers</title>
		<link rel="stylesheet" type="text/css" href="css/map_tweet.css" />
		<script src="js/map_tweet.js"></script>
	</head>
	
	<body>
	
		<header id="header">
				<div class="page-wrapper">

					<!-- MAIN NAVIGATION-->
					<nav id="main-navigation">
						<ul class="menu">
							<li><a href="./index.php">Home</a></li>
							<li><a href="./readme.html">README</a></li>
						</ul>
					</nav>

				</div>
		</header>


		<h2 id="heading">Google Maps Markers based on User Location of Searched Tweets.</h2>
	
		<!--Form for submitting Keyword for searching Tweets.-->
		<form method="post" action="index.php" class="page-wrapper">

			<fieldset id="fieldset-login">
				<legend>Search for Tweet using Keyword</legend>
				<p>As not all Tweets users have location associated with them, and the Google Map Geocoding may be unsuccessful, the actual number of 
				markers on the map will be less than the tweets retrieved by the Twitter API. Suggested keywords: <strong>vaccine</strong>, <strong>election</strong>,
				<strong>job</strong>, <strong>lockdown</strong>.</p>
				<div class="fields">
					<p class="row">
						<label for="keyword">Tweet Keyword:</label>
						<input type="text" id="keyword" name="keyword" required="required" autofocus="autofocus"/>
					</p>
					<p class="row">
						<input type="submit" class="btn btn-success" id="btn-login" name="searchTweet" value="Search Tweets"/>
					</p>
				</div>
			</fieldset>

		</form>
	
		<p class="page-wrapper"> <?= isset($search_msg)? $search_msg: ''; ?></p>
	
		<div id="map" class="page-wrapper "></div>
		
		<article class="page-wrapper "> 
			<?= isset($tweets_display)? $tweets_display: ''; ?>
		</article>
		
		<!--Even when set, this will remain hidden -->
		<p> <?= isset($list_locations)? $list_locations: ''; ?> </p>
		
		<footer>
			<p>Google Map and Twitter APIs integration developed by J. Qureshi</p>
		</footer>

		<script
			src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBds_qEOf-owL5HPT2EiO__Fp2w6eC2tVI&callback=initializeMap&libraries=&v=weekly"
			async defer
		></script>
	</body>
</html>
