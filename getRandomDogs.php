<?php
	// The GET requests to this are of the form where breed is the argument and any sub-breeds
	// are comma seperated values for that argument. If there is no argument, it will return
	// random images of any breed
	// For example this will fetch all dalmatians and the hounds of type basset and blood:
	// getRandomDogs.php?dalmatian&hound=basset,blood

	// Use the dog API class
	include("dogapi.php");
	
	const FILE_EXPIRE_TIME_SECONDS = 60;			// Fetch new image files every minute
	const FILE_EXPIRE_COUNT		   = 5;				// How many files expire after file expire time
													// Should keep this number low, because it is a GET request per new file we insert
	
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
	// Present existing images from the file cache, because doing a lot of GET requests is pretty draining
	else {
		$fileLastModified = filemtime("imagecache.txt");
		$timeDiff = time() - $fileLastModified;
		$updateImageCache = false;
		
		// File is old, update it after serving the user
		if ( $timeDiff > FILE_EXPIRE_TIME_SECONDS ) {
			$updateImageCache = true;
		}

		// Why oh why is new lines not ignored by default
		$cachedImages = file("imagecache.txt",  FILE_IGNORE_NEW_LINES);
		
		// Present the images to the user
		foreach ( $cachedImages as $dog ) {
			array_push($allImages, explode(",", $dog));
		}
		
		// Update an amount of images from the cache file
		if ( $updateImageCache ) {
			$cacheFile = fopen("imagecache.txt", "w");
			
			// Get all the breeds, did not find a way where we can use the getAllBreeds() array as an actual array
			$breeds = array();
			foreach ($dogapi->getAllBreeds() as $breed => $subBreeds) {
				array_push($breeds, $breed);
			}
			
			// Get a certain amount of new images of any breed
			foreach ( range(1, FILE_EXPIRE_COUNT) as $x ) {
				$breed = $breeds[rand(0, count($breeds))];
				array_push($cachedImages, $breed . ",," . $dogapi->getRandomImagesOfBreeds($breed, 1)[0]);
			}
			
			// Remove the 5 oldest images	
			$cachedImages = array_splice($cachedImages, FILE_EXPIRE_COUNT, count($cachedImages));

			// Insert all the images into the cache file
			foreach ( $cachedImages as $dog ) {
				if ( trim($dog) !== "" ) {
					fwrite($cacheFile, $dog . PHP_EOL);
				}
			}
			
			// Lest we forget to close the file
			fclose($cacheFile);
		}
	}
	
	// Just to make it neater, shuffle the image array before sending, that way dogs of the same breed won't follow after one another
	shuffle($allImages);
	
	// Respond back to the user
	echo json_encode(array("status" => "success", "message" => $allImages));
?>