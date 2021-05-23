
function initializeMap() {
  
  let worldCenter = { lat: 15, lng: 18 };
  let map;

	map = new google.maps.Map(document.getElementById("map"), {
		center: worldCenter,
		zoom: 2,
    } );

	const geocoder = new google.maps.Geocoder();
	
	let locations = document.getElementsByClassName("location");
	
	console.log(locations[0].innerHTML);
	console.log(locations.length);
	
	for (i = 0; i < locations.length; i++) { 
		geocodeAddress(geocoder, map, locations[i].innerHTML );
	
	}

	
}

//Part of the following function has been taken from:
//https://developers.google.com/maps/documentation/javascript/examples/geocoding-simple
function geocodeAddress(geocoder, resultsMap, address) {
        geocoder.geocode({ address: address }, (results, status) => {
          if (status === "OK") {
            new google.maps.Marker({
              map: resultsMap,
              position: results[0].geometry.location,
			  title:address
            });
          } 
        });
      }










