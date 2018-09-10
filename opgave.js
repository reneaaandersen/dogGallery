const MAX_DOG_IMAGES = 12;				// The maximum number of images to display. Any images beyond this will be switched in and out.
const IMAGE_SWITCH_TIME = 5000;			// Amount of milliseconds between switching images if there is too many
const FADE_TIME = 1000;					// Fade time in milliseconds when switching between images

var allDogs = [];						// Holds the dogs that are not displayed, but selected
var displayedDogs = [];					// Holds the dogs that are displayed and selected

// The "select dog" button
$("#selectDog").click(function(){
	// If the selector box is open when the button is pressed we refresh the images
	if ( $("#accordion").hasClass("show") ) {
		// Reset the text back to the original
		$(this).text("Select dogs");
		
		// This will become the breed list we send to the server
		var getString = "?";
		
		// Get every active button
		$("button.slider").each(function() {
			// Skip any element that's not active nor has active child elements
			if ( !($(this).hasClass("active") || $(this).hasClass("subActive")) ) {
				return true;
			}
			
			// The href Id which bootstrap uses to toggle display can also be used to see if a sub-element list exists
			var hrefId = $(this).attr("href");
			
			// If there is no sub elements, we add the breed directly
			if ( $(hrefId).length == 0 ) {
				getString += $(this).text() + "&";
				return true;
			}
			
			// If there are sub-elements we add them as a list next to the breed name
			// Also we don't need to worry about empty lists after the '=' because we already filtered out the things that weren't active/subactive :)
			getString += $(this).text() + "=";
			
			// We add every active sub-button as a sub-breed
			$(hrefId + " button.active").each(function(){
				getString += $(this)[0].innerText.trim() + ",";
			});
			
			// Trim any extra commas we might have added
			getString = trimEndChar(getString, ",") + "&";
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
	// Toggle the text and just let the framework handle the toggle display
	else {
		$(this).text("Fetch images");
	}
});

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
		console.log(value[0]);
		gridString += "<div class=\"col-sm\"><img class=\"img-thumbnail\" src=\""+value[2]+"\" data-breed=\""+value[0]+"\" data-subBreed=\""+value[1]+"\"></img></div>";
		
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

// Switches an image every IMAGE_SWITCH_TIME milliseconds
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
		$($("img")[oldIndex]).fadeOut(FADE_TIME, function() {
			$("img")[oldIndex].src = newDog[2];
		});
		// Fade in afterwards
		$($("img")[oldIndex]).fadeIn(FADE_TIME);
		$($("img")[oldIndex]).attr("data-breed", newDog[0]);
		$($("img")[oldIndex]).attr("data-subBreed", newDog[0]);
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

// Accordion click handler
$("#accordion").on("click", "button", function() {
	$("button.slider").click(function(){
		$("div.slider.show").each(function(){
			$(this).collapse("hide");
		});
	});
});


// Main element of the dog picker menu
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

// Sub elements of the dog picker menu
$("#accordion").on("click", "button.sub", function() {
	// Parent parent on the wall, I need your Id for getting the menu button for which to set the state
	var parentHref = $(this).parent().parent().attr("id");
	
	// If this button is about to be deactivated ..
	if ( $(this).hasClass("active") ) {
		// .. and there are siblings that are active, the menu button is partially active
		if ( $(this).siblings(".active").length > 0 ) {
			$("button[href$='#"+parentHref+"']").removeClass("active");
			$("button[href$='#"+parentHref+"']").addClass("subActive");
		}
		// .. and there are no siblings that are active, the menu button is not active
		else {
			$("button[href$='#"+parentHref+"']").removeClass("active");
			$("button[href$='#"+parentHref+"']").removeClass("subActive");
		}
		
		$(this).removeClass("active");
	}
	// If the button is about to be activated ..
	else {
		// .. and all the other siblings are active, then the menu button is fully active
		if ( $(this).siblings(".active").length == $(this).siblings().length ) {
			$("button[href$='#"+parentHref+"']").addClass("active");
			$("button[href$='#"+parentHref+"']").removeClass("subActive");
		}
		// .. and none or some of the siblings are active, then the menu button is partially active
		else {
			$("button[href$='#"+parentHref+"']").removeClass("active");
			$("button[href$='#"+parentHref+"']").addClass("subActive");
		}
		
		$(this).addClass("active");
	}
});

// Helper function that removes a specific character from the end of a string if it exists
function trimEndChar(string, character){
	if ( string.charAt(string.length-1) == character ) {
		return string.substring(0, string.length-1);
	}
	return string;
}

// Display a larger version of the image if it's clicked
$("#imageBox").on("click", "img", function() {
	var breed = $(this).attr("data-breed");
	var subBreed = $(this).attr("data-subBreed");
	
	if ( subBreed == "" ) {
		$("#imagePopup h4.modal-title").text(breed);
	}
	else {
		$("#imagePopup h4.modal-title").text(breed + " - " + subBreed);
	}
	
	$("#modalImage").attr("src", $(this).attr("src"));
	$("#imagePopup").modal("toggle");
});