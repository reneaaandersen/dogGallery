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
	
	if ( count($_GET) > 0 ) {
		// Iterate through all the breeds in the GET request
		foreach ( $_GET as $breed => $subBreeds ) {
			// If there are no sub-breeds, just fetch any old breed image
			if ( $subBreeds == "" ) {
				$allImages = array_merge($allImages, $dogapi->getRandomImagesOfBreeds($breed));
			}
			else {
				// Go through all the sub-breeds and get an image from each of them
				foreach ( explode(",",$subBreeds) as $subBreed ) {
					// Empty sub-breeds we skip!
					if ( $subBreed != "" ) {
						$allImages = array_merge($allImages, $dogapi->getRandomImagesOfSubBreeds($breed, $subBreed));
					}
				}
			}
		}
	}
	else {
		$allImages = $dogapi->getRandomImages(16);
	}
	
	// Just to make it neater, shuffle the image array before sending, that way the dogs won't be in single file 
	shuffle($allImages);
	
	// Respond back to the user
	echo json_encode(array("status" => "success", "message" => $allImages));
?>