<?php
	include("dogapi.php");
	
	$dogAPI = new DogAPI();
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Dog API example</title>
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
		<h1 class="text-center">Dog API example</h1>

		<button class="btn" data-toggle="collapse" href="#accordion" id="selectDog">Select dogs</button>
		<br />
		<div id="accordion" class="collapse" style="overflow:hidden; ">
		<br />
		<?php
			$counter = 0;					// Used to split the menu items on the 6th element
			$currentRowContent = "";		// Content of the current button row
			$sliderContent = "";			// Content of the slider rows
			
			foreach ($dogAPI->getAllBreeds() as $breed => $subBreeds) {
				$currentRowContent .= "<div class=\"col-2\"><button class=\"btn slider\" data-toggle=\"collapse\" href=\"#".$breed."\" style=\"width: 100%;\">".$breed."</button></div>";

				// Only add a slider for those breeds that have subbreeds
				if ( count($subBreeds) > 0 ){
					$sliderContent .= "<div style=\"overflow:auto;\"><div class=\"collapse slider\" id=\"".$breed."\">";
					$sliderContent .= "<div class=\"btn-group\">";
					foreach($subBreeds as $subBreed ) {
						$sliderContent .= "<button class=\"btn sub\">".$subBreed."</btn>";
					}
					$sliderContent .= "</div></div></div>";
				}
				
				$counter++;
				
				// Split on the 6th element
				if ( $counter % 6 == 0 ) {
					echo "<div class=\"row\">" . $currentRowContent . "</div>";
					echo "<div class=\"row\">" . $sliderContent . "</div>";
					
					$currentRowContent = "";
					$sliderContent = "";
				}
			}
		?>
		</div> 
		<br />
		<div class="container" id="imageBox">
			<!-- Free real estate for images, load them on page load -->
		</div>
		
		<div class="container">
			<p class="text-center">Project by René Andersen for an Efiware job application</p>
		</div>
	</div>
	
	<div class="modal" id="imagePopup">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title text-capitalize">Title</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<img id="modalImage" src=""></img>
				</div>
			</div>
		</div>
	</div>
		
	</body>
	<script src="opgave.js"></script>
</html>