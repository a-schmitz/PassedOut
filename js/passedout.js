$(document).ready(function(){
	var map;
	var $detailsMenu;
	var activeMarker;

	initialize();
		
	$("#place-marker").click(addMarker);
	$("#details-cancel").click(showDetailsMenu);
	$("#details-save").click(saveMarker);
	$("#details-delete").click(deleteMarker);
	
	function initialize() {
        var mapOptions = {
            center: new google.maps.LatLng(0, 0),
            zoom: 2,
            minZoom: 2,
            mapTypeId: google.maps.MapTypeId.SATELLITE
        };
        
        map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
        
        $detailsMenu = $("#marker-details");
        $(map.getDiv()).append($detailsMenu);
    }
    
    function addMarker() {       
        var marker = new PassedOutMarker(map);
        marker.addEventListener("markerClicked", markerClicked);
        marker.addEventListener("markerMouseOver", function(){console.log("mouseOver");});
        marker.addEventListener("markerMouseOut", function(){console.log("mouseOut");});        
	}
	
	function saveMarker() {
		var title = $detailsMenu.find("#details-title").val();
		var description = $detailsMenu.find("#details-description").val();		
		activeMarker.update(title, description);  
		showDetailsMenu(false);
	}
	
	function deleteMarker() {       
        activeMarker.delete();
        showDetailsMenu(false);
	}

    function showDetailsMenu(show, marker) {
    	if (activeMarker) {
    		activeMarker.setActive(false);
    	}
    	
    	var $inputTitle = $detailsMenu.find("#details-title");        
        var $inputDescription = $detailsMenu.find("#details-description");
    	
        if (show === true) {
        	activeMarker = marker;
        	$inputTitle.val(marker.title);
        	$inputDescription.val(marker.description);
            $detailsMenu.show();
        } else {        	
	        activeMarker = null;
	        $inputTitle.val("");
        	$inputDescription.val("");
        	$inputDescription.removeAttr("style");       	
            $detailsMenu.hide();
        }
    }

    function markerClicked(marker) {
        showDetailsMenu(marker.isActive, marker);
    }
});

