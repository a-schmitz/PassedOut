$(document).ready(function() {
    var map;
    var $detailsMenu;
    var activeMarker;

    initialize();

    $("#welcome").click(preventDefault);
    $("#place-marker").click(addMarker);
    $("#details-cancel").click(showDetailsMenu);
    $("#details-save").click(saveMarker);
    $("#details-delete").click(deleteMarker);
    $("#search-input").keypress(search);
    $("#search-input").focusin(removeSearchResult);
    $("#search-input").click(removeSearchResult);
    
    function initialize() {
        var mapOptions = {
            center: new google.maps.LatLng(0, 0), 
            zoom: 2,
            minZoom: 2,
            mapTypeId: google.maps.MapTypeId.HYBRID
        };

        map = new google.maps.Map($("#map-canvas")[0], mapOptions);

        $detailsMenu = $("#marker-details");
        $(map.getDiv()).append($detailsMenu);
    }

    function preventDefault(e) {
        if ($("#search-container").parent().hasClass("open")) {
            $("#search-container").dropdown('toggle');
        }
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }

    function removeSearchResult() {
        $("#search-results").remove();
    }

    function search(e) {
        if (e.which == 13) {
            var $searchEntry = $('<li role="presentation"><a href="#" tabindex="-1" role="menuitem"></a></li>');
            var $searchResults = $("#search-results");
            var addResults = false;

            if ($searchResults.length === 0) {
                $searchResults = $('<ul id="search-results" class="dropdown-menu" role="menu"></ul>');
                addResults = true;
            } else {
                $searchResults.children().remove();
            }

            var geocoder = new google.maps.Geocoder();
            geocoder.geocode(
                { 'address': $(this).val() },
                function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results.length === 1) {
                            $searchResults.remove();
                            addResults = false;
                            map.fitBounds(results[0].geometry.viewport);
                        } else {
                            for (var i = 0; i < results.length; i++) {
                                var $item = $searchEntry.clone();
                                $item.data("result", results[i]);
                                $item.click(function() {
                                    map.fitBounds($(this).data("result").geometry.viewport);
                                });
                                $item.children().text(results[i].formatted_address);
                                $searchResults.append($item);
                            }                            
                        }
                    } else {
                        $searchEntry.children().remove();
                        $searchEntry.append($("<em>&nbsp;&nbsp;No Results</em>"));
                        $searchResults.append($searchEntry);
                    }
                    if (addResults) {
                        $("#search-container").parent().append($searchResults);
                    }
                }
            );
            setTimeout(function() { $("#search-container").dropdown('toggle'); }, 0);
            return preventDefault(e);
        }
        return true;
    }

    function addMarker(e) {
        var marker = new PassedOutMarker(map);
        marker.addEventListener("markerClicked", markerClicked);
        marker.addEventListener("markerMouseOver", function() { console.log("mouseOver"); });
        marker.addEventListener("markerMouseOut", function () { console.log("mouseOut"); });

        return preventDefault(e);
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