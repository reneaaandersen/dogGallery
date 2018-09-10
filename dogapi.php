<?php

// The big ol' common function provider for interacting with the dog api at dog.ceo
class DogAPI 
{
	// The GET strings to use to fetch data from the api
	const GET_STRING_ALL_BREEDS 		= "https://dog.ceo/api/breeds/list/all";		// List of breeds and subbreeds
	const GET_STRING_RANDOM_BREED_IMAGE = "https://dog.ceo/api/breeds/image/random/";	// Get random breed image, append number to limit results
	
	// Overall handler for recieving lists
	public function getListFromGET($getString) {
		// We might get a 404 when getting breeds that does not exist, if we don't error handle we will get
		// a warning that'll mess up our JSON
		set_error_handler(function(){return array();});
		$response = file_get_contents($getString);
		restore_error_handler();
	
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