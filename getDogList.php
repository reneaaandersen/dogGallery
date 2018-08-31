<?php
	// Use the dog API class
	include("dogapi.php");
	
	$dogapi = new DogAPI();
	
	echo json_encode(array("status" => "success", "message" => $dogapi->getAllBreeds()));
?>