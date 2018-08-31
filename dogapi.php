<?php

// The big ol' common function provider for interacting with the dog api at dog.ceo
class DogAPI 
{
	// The GET strings to use to fetch data from the api
	const GET_STRING_ALL_BREEDS 		= "https://dog.ceo/api/breeds/list/all";		// List of breeds and subbreeds
	const GET_STRING_RANDOM_BREED_IMAGE = "https://dog.ceo/api/breeds/image/random/";	// Get random breed image, append number to limit results
	
	// Gets all the breeds and subbreeds and echos them as bootstrap cards
	public function getAllBreedsAsCards() {
		foreach ( $this->getAllBreeds() as $breed => $subBreedArray ) {
			echo $this->getCardHTML($breed, $subBreedArray);
		}		
	}
	
	// Gets $count random images from the api, formattet in rows and columns
	public function getRandomImagesAsGrid($count) {
		// Fetch those images Lassy!
		$response = $this->getRandomImages($count);
		
		// We need to know how many elements we've printed to pad any leftover columns
		$counter = 0;
		foreach ( $response as $image ) {
			// We have 4 rows, so for every 4th image we make a new row
			if ( $counter % 4 == 0 ) {
				echo "<div class=\"row\">";
			}
			
			// Column and image
			echo "<div class=\"col-sm\"><img class=\"img-thumbnail\" src=\"".$image."\"></img></div>";
			
			$counter++;
			
			// Increase the counter before testing if it's a 4th image again, else we'll just close the tag :|
			if ( $counter % 4 == 0 ) {
				echo "</div><br />";
			}
		}
		
		// Any empty columns is just filled with an empty box, else the row div will stretch the last images 
		// to fill itself
		while ( $counter % 4 != 0 ) {
			echo "<div class=\"col-sm\"></div>";
			$counter++;
		}
	}
	
	// Helper function to return bootstrap cards for every breed (and collapses for subbreeds)
	public function getCardHTML($breedName, $subBreeds) {
		// Header part
		$response  = "<div class=\"card\">";
		$response .= "<div class=\"card-header\">";
		$response .= "<a class=\"card-link\" data-toggle=\"collapse\" href=\"#" . $breedName . "\">";
		$response .= $breedName;
		
		// Only display the sub-breed counter if there is sub-breeds 
		// (Now one could argue we should not display it when there is only one sub-breed)
		if ( count($subBreeds) > 0 ) {
			$response .= " <span class=\"badge badge-primary badge-pill\">".count($subBreeds)."</span>";
		}
		$response .= "</a>";
		$response .= "</div>";
		
		// Collapsing header part
		$response  .= "<div id=\"" . $breedName . "\" class=\"collapse\" data-parent=\"#accordion\">";
		
		// If there are sub-breeds add them as a collapsible
		if ( count($subBreeds) > 0 ) {
			$response .= "<div class=\"card-body\">";
			$response .= "<div class=\"list-group\">";
			
			foreach ( $subBreeds as $subBreed ) {
				$response .= "<a href=\"#!\" class=\"list-group-item list-group-item-action\">".$subBreed."</a>";
			}
			
			$response .= "</div></div>";
		}
		
		$response .= "</div></div>";
		
		return $response;
	}
	
	// Overall handler for recieving lists
	public function getListFromGET($getString) {
		$response = file_get_contents($getString);
	
		// No response? Empty array!
		if ( !$response ) {
			return array();
		}

		// Server reported an error? Guess what? Empty array!
		$response = json_decode($response);	
		if ( $response->status != "success" ) {
			return array();
		}
		
		// return the array of images we got
		return $response->message;
	}
	
	// Overall handler for getting breed lists (might be redundant because it just passes along,
	// but it makes it clear what's happening)
	public function getBreedsFromGET($getString) {
		return $this->getListFromGET($getString);
	}
	
	// Get all the sub-breeds for a given breed
	public function getAllSubBreeds($subBreed){
		return $this->getBreedsFromGET("https://dog.ceo/api/breed/".$subBreed."/list");
	}
	
	// Gets a list of all the different breeds (including sub-breeds)
	public function getAllBreeds(){
		return $this->getBreedsFromGET(self::GET_STRING_ALL_BREEDS);
	}
	
	// Overall handler for getting images, (might be redundant because it just passes along,
	// but it makes it clear what's happening)
	public function getImagesFromGET($getString) {
		return $this->getListFromGET($getString);
	}
	
	// Get an array containing random images of ANY breed
	public function getRandomImages($count=4) {
		return $this->getImagesFromGET("https://dog.ceo/api/breeds/image/random/".$count);
	}
	
	// Get an array containing all dog images of a certain sub-breed
	public function getAllImagesOfSubBreeds($breedName, $subBreed) {
		return $this->getImagesFromGET("https://dog.ceo/api/breed/".$breedName."/".$subBreed."/images");
	}
	
	// Get an array containing random dog images of a certain sub-breed
	public function getRandomImagesOfSubBreeds($breedName, $subBreed, $count=4) {
		return $this->getImagesFromGET("https://dog.ceo/api/breed/".$breedName."/".$subBreed."/images/random/".$count);
	}
	
	// Get an array containing random dog images of a certain breed
	public function getRandomImagesOfBreeds($breedName, $count=4) {
		return $this->getImagesFromGET("https://dog.ceo/api/breed/".$breedName."/images/random/".$count);
	}
	
	// Get an array containing all dog images of a certain breed
	public function getAllImagesOfBreeds($breedName) {
		return $this->getImagesFromGET("https://dog.ceo/api/breed/".$breedName."/images");
	}
}
?>