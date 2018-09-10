<?php
	// The GET requests to this are of the form where breed is the argument and any sub-breeds
	// are comma seperated values for that argument. If there is no argument, it will return
	// random images of any breed
	// For example this will fetch all dalmatians and the hounds of type basset and blood:
	// getRandomDogs.php?dalmatian&hound=basset,blood

	// Use the dog API class
	include("dogapi.php");
	
	$dogapi = new DogAPI();
	
	// Every image we are to recieve
	$allImages = array();
	
	// If we are given dog breeds, fetch those specifically
	if ( count($_GET) > 0 ) {
		// Iterate through all the breeds in the GET request
		foreach ( $_GET as $breed => $subBreeds ) {
			// If there are no sub-breeds, just fetch any old breed image
			if ( $subBreeds == "" ) {
				foreach ( $dogapi->getRandomImagesOfBreeds($breed) as $image ) {
					array_push($allImages, array($breed, "", $image));
				}
			}
			else {
				// Go through all the sub-breeds and get an image from each of them
				foreach ( explode(",",$subBreeds) as $subBreed ) {
					// Empty sub-breeds we skip!
					if ( $subBreed != "" ) {
						foreach ( $dogapi->getRandomImagesOfSubBreeds($breed, $subBreed) as $image ) {
							array_push($allImages, array($breed, $subBreed, $image));
						}
					}
				}
			}
		}
	}
	else {
		// Else just get 20 random images
		$breeds = array();
		$count = 0;
		
		// Count the breeds and make an array of them, because none of the obvious cast methods nor count methods would work
		foreach ( $dogapi->getAllBreeds() as $breed => $subBreed ) {
			$count++;
			array_push($breeds, $breed);
		}
		
		// Pick 4 random breeds and get 5 images of each. If we get 20 random breeds, the load will be VERY slow due to the many get requests.
		// At this point we could hardcode the images which is bad if dog.ceo deletes the image
		// We could also save the image links in a database and update them if one of them eventually 404s, but there is no mention of DB in the assignment description
		while ( count($allImages) < 20 ) {
			$breed = $breeds[rand(0, $count-1)];
			foreach ( $dogapi->getRandomImagesOfBreeds($breed, 5) as $image ) {
				array_push($allImages, array($breed, "", $image));
			}
		}
	}
	
	// Just to make it neater, shuffle the image array before sending, that way the dogs won't be in single file 
	shuffle($allImages);
	
	// Respond back to the user
	echo json_encode(array("status" => "success", "message" => $allImages));
?>