/**
 * Instagram plugin for Craft CMS
 *
 * Instagram Field JS
 *
 * @author    Leevi Graham
 * @copyright Copyright (c) 2020 Leevi Graham
 * @link      https://newism.com.au
 * @package   Instagram
 * @since     1.0.0InstagramInstagram
 */

 ;(function ( $, window, document, undefined ) {

    var pluginName = "InstagramInstagram",
        defaults = {
        };

    // Plugin constructor
    function Plugin( element, options ) {
        this.element = element;

        this.options = $.extend( {}, defaults, options) ;

        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

    Plugin.prototype = {

        init: function(id) {
            var _this = this;

            $(function () {

                let $el = $(_this.element);
                let button = $el.find('button');
                let textArea = $el.find('textarea');

                button.on('click', function(event){
                    event.preventDefault();
                    window.open(_this.options.connectUrl);
                })

                window.addEventListener('message', function(event){
                    debugger
                    if(event.origin !== _this.options.siteUri) {
                        return;
                    }
                    textArea.val(JSON.stringify(event.data, null, '\t'));
                })

                console.log(_this.options);
            });
        }
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                new Plugin( this, options ));
            }
        });
    };

})( jQuery, window, document );
