function PassedOutMarker(map, guid, position, title, description) {
    // statics
    PassedOutMarker.markerGUIDs = [];
    PassedOutMarker.markerSprite = "./img/marker_sprite.png";
    PassedOutMarker.markerSpriteActive = "./img/marker_sprite_active.png";

    // public properties
    this.events = [];
    this.guid = guid;
    this.marker;
    this.titel = title || ""; // complications with variable name title , use titel instead
    this.description = description || "";
    this.isActive = false;

    // private properties
    var self = this;
    var map = map;
    var position = position || map.getCenter();

    init();

    function init() {        
        var newGuid;
        var save ;
        
        if (guid) {
            newGuid = guid;
            save = false;
        } else {
            do {
                newGuid = createGuid();
            } while ($.inArray(newGuid, PassedOutMarker.markerGUIDs) !== -1);
            save = true;
        }
        
        PassedOutMarker.markerGUIDs.push(newGuid);
        self.guid = newGuid;

        createMarker(save);
    }

    function createMarker(save) {
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

        if (save) {
            createAsync();
        }
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
        position = self.marker.getPosition();
        self.saveAsync();
    }

    function markerMouseOver(e) {
        self.dispatchEvent("markerMouseOver");
    }

    function markerMouseOut(e) {
        self.dispatchEvent("markerMouseOut");
    }

    function s4() {
        return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
    }

    function createGuid() {
        return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
    }
    
    function createAsync() {
        var oData = {
            guid: self.guid,
            lat: position.lat(),
            lng: position.lng(),
            title: self.titel,
            description: self.description
        };
        
        // TODO save status as in-creation

        $.ajax({
            type: "POST",
            data: oData,
        }).done(function (data) {
            // TODO check status / error handling
            console.log("create done:", data);
        }).fail(function (data) {
            // TODO error handling
            console.log("create fail:", data);
        });
    }

    this.saveAsync = function() {
        var oData = {
            guid: self.guid,
            lat: position.lat(),
            lng: position.lng(),
            title: self.titel,
            description: self.description
        };
        
        // TODO save and abort pending requests
        // TODO check if still in creating process
        // TODO check if currently deleting

        $.ajax({
            type: "PUT",
            data: oData,
        }).done(function (data) {
            // TODO check status / error handling
            console.log("save done:", data);
        }).fail(function (data) {
            // TODO error handling
            console.log("save fail:", data);
        });
    };

    this.deleteAsync = function() {
        var oData = {
            guid: self.guid,
        };
        
        // TODO cancel pending requests (update, create)
        // TODO save status as in-deletion

        $.ajax({
            type: "DELETE",
            data: oData,
        }).done(function (data) {
            // TODO check status / error handling
            console.log("delete done:", data);
        }).fail(function (data) {
            // TODO error handling
            console.log("delete fail:", data);
        });
    };
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
    this.titel = title || "";
    this.description = description || "";
    this.saveAsync();
};
PassedOutMarker.prototype.delete = function() {
    var index = PassedOutMarker.markerGUIDs.indexOf(this.guid);
    PassedOutMarker.markerGUIDs.splice(index, 1);
    this.marker.setMap(null);
    this.deleteAsync();
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