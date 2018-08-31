const MAX_DOG_IMAGES = 12;				// Display a maximum of 12 images
const IMAGE_SWITCH_TIME = 5000;			// Switch image every 5 seconds

// allDogs and displayedDogs are the selected images
var allDogs = [];						// Holds the dogs that are not displayed, but selected
var displayedDogs = [];					// Holds the dogs that are displayed and selected

// Click handler for a card list element
$("#accordion").on("click", ".card-link", function() {
	// If there are children, don't color anything, just let the element expand. But if there aren't ...
	if ( $(this).parent().siblings()[0].childElementCount == 0 ) {
		// Check if it's already active or not, if it is, deactivate it
		if ( $(this).parent().hasClass("active") ) {
			$(this).parent().removeClass("active");
		}
		// Activate it otherwise
		else {
			$(this).parent().addClass("active");
		}
	}
});

// Click handler for a child element of a card list element
$("#accordion").on("click", ".list-group-item", function() {
	// Already active?
	if ( $(this).hasClass("active") ) {
		// Deactivate it!
		$(this).removeClass("active");
		
		// Check if there are other active child elements
		if ( $(this).siblings(".active").length > 0 ) {
			// Set it as partially active
			$(this).parentsUntil("#accordion").children(".card-header").removeClass("active");
			$(this).parentsUntil("#accordion").children(".card-header").addClass("subActive");
		}
		// No active children?
		else if ( $(this).siblings(".active").length == 0 ) {
			// No activity at all for the parent
			$(this).parentsUntil("#accordion").children(".card-header").removeClass("subActive");
			$(this).parentsUntil("#accordion").children(".card-header").removeClass("active");
		}
	}
	// Not active?
	else {
		// Check if all the other siblings are active 
		if ( $(this).siblings(".active").length == $(this).siblings().length ) {
			// If that's the case set the parent as fully active (fully selected)
			$(this).parentsUntil("#accordion").children(".card-header").removeClass("subActive");
			$(this).parentsUntil("#accordion").children(".card-header").addClass("active");
		}
		// If there are no other active children (and indirectly at least one other sibling), set it partially active
		else if ( $(this).siblings(".active").length == 0 ) {
			$(this).parentsUntil("#accordion").children(".card-header").addClass("subActive");
		}
		
		// Activate the child itself!
		$(this).addClass("active");
	}
});

// If a string ends with a specific character, remove the character
function trimEndChar(string, character){
	if ( string.charAt(string.length-1) == character ) {
		return string.substring(0, string.length-2);
	}
	return string;
}

// The "select dog" button
$("#selectDog").click(function(){
	// If the selector box is open when the button is pressed we refresh the images
	if ( $("#accordion").hasClass("show") ) {
		// This will be the breed list we send to the server
		var getString = "?";
		
		// Go through every card
		$(".card-header").each(function(){
			// If it's active or has a child that's active
			if ( $(this).hasClass("active") || $(this).hasClass("subActive") ) {			
				// Add the breed to the list
				getString += $(this).children("a").attr("href").substring(1) + "=";
				
				// If any of the children is selected, add them to the list too
				$($(this).children("a").attr("href") + " a.active").each(function() {
					getString += $(this).text() + ","
				});
				
				// Trim any extra commas we might have added
				getString = trimEndChar(getString, ",");
				
				// Prepare for next breed
				getString += "&";
			}
		});
		
		// Trim any extra ampersands we might have added
		getString = trimEndChar(getString, "&");
		
		// Make sure that the images don't switch while we're loading the new images 
		clearTimeout(switchImageTimer);
		
		// And just to make the change not look too abrupt, fade the whole image box
		$("#imageBox").fadeOut();
		
		// Get the doggy list
		$.get("getRandomDogs.php" + getString, function(data){
			allDogs = JSON.parse(data).message;
			displayedDogs = allDogs.slice(0,MAX_DOG_IMAGES);
			allDogs = allDogs.slice(MAX_DOG_IMAGES);
			$("#imageBox").text("");
			$("#imageBox").append(imagesToGrid(displayedDogs));
			$("#imageBox").fadeIn();
			switchImageTimer = window.setTimeout(switchImage, IMAGE_SWITCH_TIME);
		});
	}
});

function breedArrayToMenuItems(breedArray) {
	var output = "";
	var currentRowContent = "";
	var sliderContent = "";
	var counter = 0;
	
	$.each(breedArray, function(breed, subBreeds) {
		currentRowContent += "<div class=\"col-2\"><button class=\"btn slider\" data-toggle=\"collapse\" href=\"#"+breed+"\" style=\"width: 100%;\">"+breed+"</button></div>";

		if ( subBreeds.length > 0  ){
			sliderContent += "<div style=\"overflow:auto;\"><div class=\"collapse slider\" id=\""+breed+"\">";
			sliderContent += "<div class=\"btn-group\">";
			$.each(subBreeds, function(index, subBreed){
				sliderContent += "<button class=\"btn sub\">"+subBreed+"</btn>&nbsp";
			});
			sliderContent += "</div>";
			sliderContent += "</div>";
			sliderContent += "</div>";
		}
		
		counter++;
		
		if ( counter % 6 == 0 ) {
			output += "<div class=\"row\">" + currentRowContent + "</div>";
			output += sliderContent;
			
			currentRowContent = "";
			sliderContent = "";
		}
	});
	
	return output;
}

// Transforms an image array into a grid string
function imagesToGrid(imageList) {
	var counter = 0;
	var gridString = "";
	
	// Iterate over the whole array
	$.each(imageList, function(index, value){
		// For every 4th image we start a new row
		if ( counter%4 == 0 ) {
			gridString += "<div class=\"row\">";
		}
		
		// Add a column with the image
		gridString += "<div class=\"col-sm\"><img class=\"img-thumbnail\" src=\""+value+"\"></img></div>";
		
		// Increase the counter before the end tag, else we will start and end in the same iteration
		counter++;
				
		// And we end a row for every 4th image
		if ( counter%4 == 0 ) {
			gridString += "</div><br />";
		}
	});
	
	// Pad the grid, else the data will be stretched to fill the row
	while ( counter%4 != 0 ) {
		gridString += "<div class=\"col-sm\"></div>";
		counter++;
	}
	
	// Lest we forget the last div end tag
	return gridString + "</div>";
}

function switchImage() {
	// Only switch if there are actually images to switch to!
	if ( allDogs.length > 0 ) {
		var newIndex = Math.floor(Math.random()*allDogs.length);
		var newDog = allDogs[newIndex];
		
		var oldIndex = Math.floor(Math.random()*MAX_DOG_IMAGES);
		var oldDog = displayedDogs[oldIndex];
		
		// Swap the two elements in the indexes
		displayedDogs[oldIndex] = newDog;
		allDogs[newIndex] = oldDog;
		
		// Fade the old image out and use the callback afterwards to change the image
		$($("img")[oldIndex]).fadeOut(1000, function() {
			$("img")[oldIndex].src = newDog;
		});
		// Fade in afterwards
		$($("img")[oldIndex]).fadeIn(1000);
	}
	
	// Ensure that the timer repeats next time
	switchImageTimer = window.setTimeout(switchImage, IMAGE_SWITCH_TIME);
}

// Load some doggies when the page finished loading /^(ô_ô)^\  <- beagle not a spider I swear!
$.get("getRandomDogs.php", function(data){
	// Clear the image box and add the new grid
	allDogs = JSON.parse(data).message;
	displayedDogs = allDogs.slice(0,MAX_DOG_IMAGES);
	allDogs = allDogs.slice(MAX_DOG_IMAGES);
	$("#imageBox").text("");
	$("#imageBox").append(imagesToGrid(displayedDogs));
});

// Start the initial switch image timer
switchImageTimer = window.setTimeout(switchImage, IMAGE_SWITCH_TIME);

$.get("getDogList.php", function(data){
	$("#accordion").text("");
	$("#accordion").append("<br />");
	$("#accordion").append(breedArrayToMenuItems(JSON.parse(data).message));
});


$("#accordion").on("click", "button", function() {
	$("button.slider").click(function(){
		$("div.slider.show").each(function(){
			$(this).collapse("hide");
		});
	});
});


$("#accordion").on("click", "button.slider", function() {
	var targetId = $(this).attr("href");
	if ( $(targetId).length == 0 ) {
		if ( $(this).hasClass("active") ) {
			$(this).removeClass("active");
		}
		else {
			$(this).addClass("active");
		}
	}
});

$("#accordion").on("click", "button.sub", function() {
	var parentHref = $(this).parent().parent().attr("id");
	
	if ( $(this).hasClass("active") ) {
		if ( $(this).siblings(".active").length > 0 ) {
			$("button[href$='#"+parentHref+"']").removeClass("active");
			$("button[href$='#"+parentHref+"']").addClass("subActive");
		}
		else {
			$("button[href$='#"+parentHref+"']").removeClass("active");
			$("button[href$='#"+parentHref+"']").removeClass("subActive");
		}
		
		$(this).removeClass("active");
	}
	else {
		
		if ( $(this).siblings(".active").length == $(this).siblings().length ) {
			$("button[href$='#"+parentHref+"']").addClass("active");
			$("button[href$='#"+parentHref+"']").removeClass("subActive");
		}
		else {
			$("button[href$='#"+parentHref+"']").removeClass("active");
			$("button[href$='#"+parentHref+"']").addClass("subActive");
		}
		
		$(this).addClass("active");
	}
});