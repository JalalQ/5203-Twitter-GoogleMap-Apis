# Introduction
Integration of the Twitter and Google Map APIs. Based on the search query for tweets, the app, places marker on the map for the location of the users tweeting the tweets matching the search query.

The API project makes use of two APIs, Twitter API, and Google Map Javascript, and Geocoding API. 
- [x] The user [searches for tweets](https://developer.twitter.com/en/docs/twitter-api/tweets/search/introduction) based on keyword. The Twitter API return tweets which has the search query in the tweets. The **location field value** from the [user object](https://developer.twitter.com/en/docs/twitter-api/data-dictionary/object-model/user) of these tweets is fetched.
- [x] The location value is then passed to [Google Map GeoCoding API](https://developers.google.com/maps/documentation/geocoding/start) which tries to retrieve the latlong values for each of the valid locations.
- [x] The latlong values are then plotted on the map with a marker, using Google Map Javascript API.
- [x] Tweets matching the search query are also listed along with the username and time of posting are also listed below the map.

The Google Map API key found in the index.html is HTTP referrer restricted.

## Screenshots

![tweet_results](https://user-images.githubusercontent.com/58306478/119267608-64d7a800-bbbd-11eb-8737-a25ee93032d8.png)

![readme](https://user-images.githubusercontent.com/58306478/119267778-03640900-bbbe-11eb-898e-bfdd990a1664.png)

![postman-twitter](https://user-images.githubusercontent.com/58306478/119267612-66a16b80-bbbd-11eb-93f6-2134ceee7193.png)
