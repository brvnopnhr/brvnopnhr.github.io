(function(Eventgallery, jQuery){
	
Eventgallery.Cart = function(newOptions) {
	this.cart = [];
	this.isMultiline = false;
    this.options = {
        buttonShowType: 'block',
        emptyCartSelector: '.eventgallery-cart-empty',
        cartSelector: '.eventgallery-ajaxcart',
        cartItemContainerSelector: '.cart-items-container',
        cartItemsSelector: '.eventgallery-ajaxcart .cart-items',
        cartItemSelector: '.eventgallery-ajaxcart .cart-items .cart-item',
        cartCountSelector: '.itemscount',
        buttonDownSelector: '.toggle-down',
        buttonUpSelector: '.toggle-up',
        cartItemsMinHeight: null,
        removeUrl: "",
        add2cartUrl: "",
        getCartUrl: "",
        removeLinkTitle: "Remove"
    };
    this.options = Eventgallery.Tools.mergeObjects(this.options, newOptions);
    this.initialize();
};

Eventgallery.Cart.prototype.slideUp = function() {
    jQuery(this.options.cartItemContainerSelector).animate({height: this.options.cartItemsMinHeight});
    jQuery(this.options.buttonUpSelector).css('display', 'none');
    if (this.isMultiline) {
    	jQuery(this.options.buttonDownSelector).css('display', this.options.buttonShowType);
    } else {
    	jQuery(this.options.buttonDownSelector).css('display', 'none');
    }
};

Eventgallery.Cart.prototype.slideDown = function() {
    jQuery(this.options.cartItemContainerSelector).animate({height: jQuery(this.options.cartItemsSelector).height()});
    jQuery(this.options.buttonDownSelector).css('display', 'none');
    jQuery(this.options.buttonUpSelector).css('display', this.options.buttonShowType);
};

Eventgallery.Cart.prototype.initialize = function() {

    jQuery(this.options.buttonDownSelector).click(jQuery.proxy(function (event) {
        event.preventDefault();
        this.slideDown();
    }, this));

    jQuery(this.options.buttonUpSelector).click(jQuery.proxy(function (event) {
        event.preventDefault();
        this.slideUp();
    }, this));

    jQuery("body").off('click', '.eventgallery-add2cart');
    jQuery("body").on ('click', '.eventgallery-add2cart', jQuery.proxy(this.add2cart, this));

    jQuery("body").off('click', '.eventgallery-add-all');
    jQuery("body").on ('click', '.eventgallery-add-all', jQuery.proxy(this.addAll2cart, this));

    jQuery("body").off('click', '.eventgallery-removeFromCart');
    jQuery("body").on ('click', '.eventgallery-removeFromCart', jQuery.proxy(this.removeFromCart, this));

    jQuery("body").on('updatecartlinks', jQuery.proxy(function (event) {
        this.populateCart(true);
    }, this));

    jQuery("body").on('updatecart', jQuery.proxy(function (event, cart) {
        this.cart = cart;
        this.populateCart(false);
    }, this));

    this.updateCart();
};

Eventgallery.Cart.prototype.updateCartItemContainer = function () {

    // detect multiple rows
	
    this.isMultiline = false;
    var y = -1;
	var currentObject =  this;
		
    jQuery(this.options.cartItemSelector).each(function () {
        var posY = jQuery(this).position().top;
        if (y < 0) {
            y = posY;
        } else if (y != posY) {
            currentObject.isMultiline = true;
        }
    });	

    if (this.isMultiline) {
        // prevent showing the wrong button. Basically this is an inital action if a second row is created.
    	var down = jQuery(this.options.buttonDownSelector);
    	var up = jQuery(this.options.buttonUpSelector);
        
        if (down.css('display') == 'none' && up.css('display') == 'none') {
            down.css('display', this.options.buttonShowType);
        } else {
            // update if a third or more row is created
            if (up.css('display') != 'none') {
                // timeout to avoid any size issues because of a slow browser
                setTimeout(jQuery.proxy(function() {
                    this.slideDown();
                }, this), 1000);
            }
        }
    } else {
        this.slideUp();
    }
};

Eventgallery.Cart.prototype.populateCart = function (linksOnly) {

    if (this.cart.length === 0) {
        jQuery(this.options.cartSelector).slideUp();
        jQuery(this.options.emptyCartSelector).slideDown();
    } else {
        jQuery(this.options.cartSelector).slideDown();
        jQuery(this.options.emptyCartSelector).slideUp();
    }
    // define where all the cart html items are located

    var cartHTML = jQuery(this.options.cartItemsSelector);
    if (cartHTML === null) {
        return;
    }
    // clear the html showing the current cart
    if (!linksOnly) {
        cartHTML.html("");
    }

    // reset cart button icons
    jQuery('a.eventgallery-add2cart').addClass('button-add2cart').removeClass('button-alreadyInCart');


    for (var i = this.cart.length - 1; i >= 0; i--) {
        //create the id. It's always folder=foo&file=bar
        var id = 'folder=' + this.cart[i].folder + '&file=' + this.cart[i].file;
        //add the item to the cart. Currently we simple refresh the whole cart.
        if (!linksOnly) {
            cartHTML.html(cartHTML.html() +
                '<div class="cart-item"><span class="badge">'+this.cart[i].count+'</span>' + 
                    this.cart[i].imagetag + 
                    '<a href="#" title="' + this.options.removeLinkTitle + '" class="eventgallery-removeFromCart button-removeFromCart" data-id="lineitemid=' + this.cart[i].lineitemid + '">'+
                    '<i class="big"></i>' +
                    '</a></div>');
        }
        // mark the add2cart link to show the item is already in the cart
        jQuery('a.eventgallery-add2cart[data-id="' + id + '"]').addClass('button-alreadyInCart').removeClass('button-add2cart');

    }

    if (!linksOnly) {
        cartHTML.html(cartHTML.html() + '<div style="clear:both"></div>');
        if (null === this.options.cartItemsMinHeight) {
            this.options.cartItemsMinHeight = jQuery(this.options.cartItemContainerSelector).height();
        }
        this.updateCartItemContainer();
    }

    jQuery('.itemscount').html(this.cart.length);
    
    jQuery("a[data-eventgallery-lightbox='cart']").eventgallery_colorbox.close();
	jQuery("a[data-eventgallery-lightbox='cart']").eventgallery_colorbox({photo: true, maxWidth: '90%', maxHeight: '90%', rel: 'cart'});
};

Eventgallery.Cart.prototype.updateCart = function () {
    jQuery.getJSON(
        this.options.getCartUrl,
        {json: 'yes'},
        function (data) {
            if (data !== undefined) {
                jQuery("body").trigger( 'updatecart', [data] );
            }
        }
    );
};

Eventgallery.Cart.prototype.removeFromCart = function (event) {
	return this.doRequest(event, this.options.removeUrl);
};

Eventgallery.Cart.prototype.add2cart = function (event) {
    var radioButtons = jQuery('input:checked[name=currentimagetype]');
    if (radioButtons.length>0) {
        if (event.target.tagName == 'A') {
            linkElement = jQuery(event.target);
        } else {
            linkElement = jQuery(event.target).parent('A');
        }
        
        var data = linkElement.data('id');
        data = data + '&imagetypeid=' + radioButtons[0].value;

        return this.doRequest(event, this.options.add2cartUrl, data);
    } else {
        return this.doRequest(event, this.options.add2cartUrl);
    }
};

Eventgallery.Cart.prototype.addAll2cart = function (event) {
    jQuery('.eventgallery-add2cart').click();
};

Eventgallery.Cart.prototype.doRequest = function (event, url, data) {

    event.preventDefault();

    var linkElement;

    if (event.target.tagName == 'A') {
        linkElement = jQuery(event.target);
    } else {
        linkElement = jQuery(event.target).parent('A');
    }

    var iconElement = linkElement.children('i');
    if (data === undefined) {
        data = linkElement.data('id');
    }

    iconElement.addClass('loading');

    var oldBtnClass = "";
    if (linkElement.hasClass('btn-primary')) {
        oldBtnClass = 'btn-primary';
        linkElement.removeClass(oldBtnClass);
        linkElement.addClass('btn-info');
    }

    jQuery.getJSON(
        url,
        data,
        function (data) {

            if (data !== undefined) {
                jQuery("body").trigger( 'updatecart', [data] );
            }

            iconElement.removeClass('loading');

            if (oldBtnClass.length>0) {
                linkElement.removeClass('btn-info');
                linkElement.addClass('btn-success');
                setTimeout(function() {
                    linkElement.removeClass('btn-success');
                    linkElement.addClass(oldBtnClass);
                }, 2000);
            }
        }
    );

    return true;
};

})(Eventgallery, Eventgallery.jQuery);