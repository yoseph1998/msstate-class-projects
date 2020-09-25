// Whent the location is changed, retrieve and display all items
function showItems(location) {
	document.getElementById("output").innerHTML = "";
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById("output").innerHTML = this.responseText;
			var rows = document.getElementsByTagName("tr");
			for(var i = 0; i < rows.length; i++) {
				
			}
		}
	};

	xmlhttp.open("GET", "php/itemDisplay.php?location="+location, true);
	xmlhttp.send();
}

// When the search field is submitted, retieve and display all relevenat items
function showSearch() {
	document.getElementById("locBtn").innerHTML = 'Location <i class="fa fa-caret-down"></i>';
	document.getElementById("output").innerHTML = "";
	var searchData = document.getElementById("searchfield").value;
	console.log(searchData);
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById("output").innerHTML = this.responseText;
		}
	};
	xmlhttp.open("GET", "php/searchDisplay.php?search="+searchData, true);
	xmlhttp.send();
}


/* When the user clicks on the button, toggle between hiding and showing the dropdown content */
function dropdownMenu() {
	document.getElementById("dropdownList").classList.toggle("show");
}

// When user switches location, display new location
function changeLocale() {
	var anchors = document.getElementsByTagName("a");

	for(var i = 0; i < anchors.length; i++) {
		if (anchors[i].className == "locale") {
			anchors[i].onclick = function() {
				var location = this.id;
				console.log(this.id);
				document.getElementById("locBtn").innerHTML = this.id + ' <i class="fa fa-caret-down"></i>';
				showItems(location);
				return false;
			}
		}
	}
}


// Close the dropdown menu if the user clicks outside of it
window.onclick = function(event) {
	if (!event.target.matches('.dropbtn')) {

		var dropdowns = document.getElementById("dropdownList");
		if (dropdowns.classList.contains('show')) {
			dropdowns.classList.remove('show');
		}
	}
}