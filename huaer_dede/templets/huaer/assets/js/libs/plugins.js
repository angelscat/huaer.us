/*!
 * jQuery Cookie Plugin
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2011, Klaus Hartl
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.opensource.org/licenses/GPL-2.0
 */
(function(g){g.cookie=function(h,b,a){if(1<arguments.length&&(!/Object/.test(Object.prototype.toString.call(b))||null===b||void 0===b)){a=g.extend({},a);if(null===b||void 0===b)a.expires=-1;if("number"===typeof a.expires){var d=a.expires,c=a.expires=new Date;c.setDate(c.getDate()+d)}b=""+b;return document.cookie=[encodeURIComponent(h),"=",a.raw?b:encodeURIComponent(b),a.expires?"; expires="+a.expires.toUTCString():"",a.path?"; path="+a.path:"",a.domain?"; domain="+a.domain:"",a.secure?"; secure":
""].join("")}for(var a=b||{},d=a.raw?function(a){return a}:decodeURIComponent,c=document.cookie.split("; "),e=0,f;f=c[e]&&c[e].split("=");e++)if(d(f[0])===h)return d(f[1]||"");return null}})(jQuery);

$.scrollTo = $.fn.scrollTo = function(x, y, options){
    if (!(this instanceof $)) return $.fn.scrollTo.apply($('html, body'), arguments);

    options = $.extend({}, {
        gap: {
            x: 0,
            y: 0
        },
        animation: {
            easing: 'swing',
            duration: 300,
            complete: $.noop,
            step: $.noop
        }
    }, options);

    return this.each(function(){
        var elem = $(this);
        elem.stop().animate({
            scrollLeft: !isNaN(Number(x)) ? x : $(y).offset().left + options.gap.x,
            scrollTop: !isNaN(Number(y)) ? y : $(y).offset().top + options.gap.y
        }, options.animation);
    });
};
