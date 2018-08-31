<?php
	include("dogapi.php");
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Watch dogs</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
		<link rel="stylesheet" href="opgave.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
	</head>
	
	<body>
	<div class="container">
		<h1>DOGS</h1>

		<button class="btn" data-toggle="collapse" href="#accordion" id="selectDog">Select dogs</button>
		<br /><br />
		<div id="accordion" class="collapse" style="">
			<!-- Free real estate for dog breeds and sub-breeds -->
		</div> 
		
		<div class="container" id="imageBox">
			<!-- Free real estate for images, load on page load -->
		</div>
	</div>
		
	</body>
	<script src="opgave.js"></script>
</html>