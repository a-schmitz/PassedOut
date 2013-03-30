function PassedOutMarker(map, position, title, description) {
    // statics
    PassedOutMarker.markerGUIDs = [];
    PassedOutMarker.markerSprite = "./img/marker_sprite.png";
    PassedOutMarker.markerSpriteActive = "./img/marker_sprite_active.png";

    // public properties
    this.events = [];
    this.guid;
    this.marker;
    this.title = title || "";
    this.description = description || "";
    this.isActive = false;

    // private properties
    var self = this;
    var map = map;
    var position = position || map.getCenter();

    init();

    function init() {
        var newGuid;
        do {
            newGuid = guid();
        } while ($.inArray(newGuid, PassedOutMarker.markerGUIDs) !== -1);

        PassedOutMarker.markerGUIDs.push(newGuid);
        self.guid = newGuid;

        createMarker();
    }

    function createMarker() {
        var icon = new google.maps.MarkerImage(PassedOutMarker.markerSprite, new google.maps.Size(20, 34), new google.maps.Point(0, 0));

        self.marker = new google.maps.Marker({
            position: position,
            draggable: true,
            map: map,
            icon: icon
        });

        google.maps.event.addListener(self.marker, "click", markerClicked);
        google.maps.event.addListener(self.marker, "rightclick", markerClicked);
        google.maps.event.addListener(self.marker, "dragend", markerDragged);
        google.maps.event.addListener(self.marker, "mouseover", markerMouseOver);
        google.maps.event.addListener(self.marker, "mouseout", markerMouseOut);

        // TODO create via webserivce
    }

    function markerClicked(e) {
        if (self.isActive) {
            self.setActive(false);
        } else {
            self.setActive(true);
        }
        self.dispatchEvent("markerClicked");
    }

    function markerDragged(e) {
        self.position = self.marker.getPosition();
        // TODO update position via webservice
    }

    function markerMouseOver(e) {
        //var $contextMenu = $('<div tabindex="-1" class="popover fade in"></div>');      

        //var $arrow = $('<div class="arrow"></div>' +
        //'<h3 class="popover-title">A Title</h3>'+
        //'<div class="popover-content">And heres some amazing content. Its very engaging. right?</div>');
        //$contextMenu.append($arrow);

        //$(map.getDiv()).append($contextMenu);
        self.dispatchEvent("markerMouseOver");
    }

    function markerMouseOut(e) {
        self.dispatchEvent("markerMouseOut");
    }

    function s4() {
        return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
    }

    function guid() {
        return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
    }
}

PassedOutMarker.prototype.setActive = function(active) {
    var icon;
    if (active) {
        icon = new google.maps.MarkerImage(PassedOutMarker.markerSpriteActive, new google.maps.Size(20, 34), new google.maps.Point(0, 0));
    } else {
        icon = new google.maps.MarkerImage(PassedOutMarker.markerSprite, new google.maps.Size(20, 34), new google.maps.Point(0, 0));
    }
    this.marker.setIcon(icon);
    this.isActive = active;
};
PassedOutMarker.prototype.update = function(title, description) {
    this.title = title || "";
    this.description = description || "";
    // TODO update via webservice
};
PassedOutMarker.prototype.delete = function() {
    var index = PassedOutMarker.markerGUIDs.indexOf(this.guid);
    PassedOutMarker.markerGUIDs.splice(index, 1);
    this.marker.setMap(null);
    // TODO delete via webservice
};
PassedOutMarker.prototype.addEventListener = function(event, callback) {
    this.events[event] = this.events[event] || [];
    if (this.events[event]) {
        this.events[event].push(callback);
    }
};
PassedOutMarker.prototype.removeEventlistener = function(event, callback) {
    if (this.events[event]) {
        var listeners = this.events[event];
        for (var i = listeners.length - 1; i >= 0; --i) {
            if (listeners[i] === callback) {
                listeners.splice(i, 1);
                return true;
            }
        }
    }
    return false;
};
PassedOutMarker.prototype.dispatchEvent = function(event) {
    if (this.events[event]) {
        var listeners = this.events[event], len = listeners.length;
        while (len--) {
            listeners[len](this);
        }
    }
};