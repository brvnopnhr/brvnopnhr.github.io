var Eventgallery = Eventgallery || {};

Eventgallery.jQuery = eventgallery.jQuery;

(function(Eventgallery){

Eventgallery.Tools = {};

Eventgallery.Tools.mergeObjects = function(defaults, options) {
	if (options === null || defaults === null) {
    	return defaults;
    }
    
    for (var key in options) {
	  defaults[key] = options[key];
    }
    
	return defaults;
};

/**
* calculates the border of the given elements with the given properties
*/
Eventgallery.Tools.calcBorderWidth = function(elements, properties) {
    var sum = 0;

    for (var i=0; i<elements.length; i++) {
        for (var j=0; j<properties.length; j++) {
            var value = parseFloat( elements[i].css(properties[j]) );
            if (!isNaN(value)) {
                sum += value;
            }
        }
    }
    
    return sum;    
};

})(Eventgallery);   