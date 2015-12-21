jQuery.noConflict();
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/** ******  left menu  *********************** **/
jQuery(function () {
    jQuery('#sidebar-menu li ul').slideUp();
    jQuery('#sidebar-menu li').removeClass('active');

    jQuery('#sidebar-menu li').click(function () {
        if (jQuery(this).is('.active')) {
            jQuery(this).removeClass('active');
            jQuery('ul', this).slideUp();
            jQuery(this).removeClass('nv');
            jQuery(this).addClass('vn');
        } else {
            jQuery('#sidebar-menu li ul').slideUp();
            jQuery(this).removeClass('vn');
            jQuery(this).addClass('nv');
            jQuery('ul', this).slideDown();
            jQuery('#sidebar-menu li').removeClass('active');
            jQuery(this).addClass('active');
        }
    });

    jQuery('#menu_toggle').click(function () {
        if (jQuery('body').hasClass('nav-md')) {
            jQuery('body').removeClass('nav-md');
            jQuery('body').addClass('nav-sm');
            jQuery('.left_col').removeClass('scroll-view');
            jQuery('.left_col').removeAttr('style');
            jQuery('.sidebar-footer').hide();

            if (jQuery('#sidebar-menu li').hasClass('active')) {
                jQuery('#sidebar-menu li.active').addClass('active-sm');
                jQuery('#sidebar-menu li.active').removeClass('active');
            }
        } else {
            jQuery('body').removeClass('nav-sm');
            jQuery('body').addClass('nav-md');
            jQuery('.sidebar-footer').show();

            if (jQuery('#sidebar-menu li').hasClass('active-sm')) {
                jQuery('#sidebar-menu li.active-sm').addClass('active');
                jQuery('#sidebar-menu li.active-sm').removeClass('active-sm');
            }
        }
    });
});

/* Sidebar Menu active class */
jQuery(function () {
    var url = window.location;
    jQuery('#sidebar-menu a[href="' + url + '"]').parent('li').addClass('current-page');
    jQuery('#sidebar-menu a').filter(function () {
        return this.href == url;
    }).parent('li').addClass('current-page').parent('ul').slideDown().parent().addClass('active');
});

/** ******  /left menu  *********************** **/



/** ******  tooltip  *********************** **/
jQuery(function () {
        //jQuery('[data-toggle="tooltip"]').tooltip()
    })
    /** ******  /tooltip  *********************** **/
    /** ******  progressbar  *********************** **/
if (jQuery(".progress .progress-bar")[0]) {
    jQuery('.progress .progress-bar').progressbar(); // bootstrap 3
}
/** ******  /progressbar  *********************** **/
/** ******  switchery  *********************** **/
if (jQuery(".js-switch")[0]) {
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    elems.forEach(function (html) {
        var switchery = new Switchery(html, {
            color: '#26B99A'
        });
    });
}
/** ******  /switcher  *********************** **/
/** ******  collapse panel  *********************** **/
// Close ibox function
jQuery('.close-link').click(function () {
    var content = jQuery(this).closest('div.x_panel');
    content.remove();
});

// Collapse ibox function
jQuery('.collapse-link').click(function () {
    var x_panel = jQuery(this).closest('div.x_panel');
    var button = jQuery(this).find('i');
    var content = x_panel.find('div.x_content');
    content.slideToggle(200);
    (x_panel.hasClass('fixed_height_390') ? x_panel.toggleClass('').toggleClass('fixed_height_390') : '');
    (x_panel.hasClass('fixed_height_320') ? x_panel.toggleClass('').toggleClass('fixed_height_320') : '');
    button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
    setTimeout(function () {
        x_panel.resize();
    }, 50);
});
/** ******  /collapse panel  *********************** **/
/** ******  iswitch  *********************** **/
if (jQuery("input.flat")[0]) {
    jQuery(document).ready(function () {
        jQuery('input.flat').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });
    });
}
/** ******  /iswitch  *********************** **/
/** ******  star rating  *********************** **/
// Starrr plugin (https://github.com/dobtco/starrr)
var __slice = [].slice;

(function (jQuery, window) {
    var Starrr;

    Starrr = (function () {
        Starrr.prototype.defaults = {
            rating: void 0,
            numStars: 5,
            change: function (e, value) {}
        };

        function Starrr(jQueryel, options) {
            var i, _, _ref,
                _this = this;

            this.options = jQuery.extend({}, this.defaults, options);
            this.jQueryel = jQueryel;
            _ref = this.defaults;
            for (i in _ref) {
                _ = _ref[i];
                if (this.jQueryel.data(i) != null) {
                    this.options[i] = this.jQueryel.data(i);
                }
            }
            this.createStars();
            this.syncRating();
            this.jQueryel.on('mouseover.starrr', 'span', function (e) {
                return _this.syncRating(_this.jQueryel.find('span').index(e.currentTarget) + 1);
            });
            this.jQueryel.on('mouseout.starrr', function () {
                return _this.syncRating();
            });
            this.jQueryel.on('click.starrr', 'span', function (e) {
                return _this.setRating(_this.jQueryel.find('span').index(e.currentTarget) + 1);
            });
            this.jQueryel.on('starrr:change', this.options.change);
        }

        Starrr.prototype.createStars = function () {
            var _i, _ref, _results;

            _results = [];
            for (_i = 1, _ref = this.options.numStars; 1 <= _ref ? _i <= _ref : _i >= _ref; 1 <= _ref ? _i++ : _i--) {
                _results.push(this.jQueryel.append("<span class='glyphicon .glyphicon-star-empty'></span>"));
            }
            return _results;
        };

        Starrr.prototype.setRating = function (rating) {
            if (this.options.rating === rating) {
                rating = void 0;
            }
            this.options.rating = rating;
            this.syncRating();
            return this.jQueryel.trigger('starrr:change', rating);
        };

        Starrr.prototype.syncRating = function (rating) {
            var i, _i, _j, _ref;

            rating || (rating = this.options.rating);
            if (rating) {
                for (i = _i = 0, _ref = rating - 1; 0 <= _ref ? _i <= _ref : _i >= _ref; i = 0 <= _ref ? ++_i : --_i) {
                    this.jQueryel.find('span').eq(i).removeClass('glyphicon-star-empty').addClass('glyphicon-star');
                }
            }
            if (rating && rating < 5) {
                for (i = _j = rating; rating <= 4 ? _j <= 4 : _j >= 4; i = rating <= 4 ? ++_j : --_j) {
                    this.jQueryel.find('span').eq(i).removeClass('glyphicon-star').addClass('glyphicon-star-empty');
                }
            }
            if (!rating) {
                return this.jQueryel.find('span').removeClass('glyphicon-star').addClass('glyphicon-star-empty');
            }
        };

        return Starrr;

    })();
    return jQuery.fn.extend({
        starrr: function () {
            var args, option;

            option = arguments[0], args = 2 <= arguments.length ? __slice.call(arguments, 1) : [];
            return this.each(function () {
                var data;

                data = jQuery(this).data('star-rating');
                if (!data) {
                    jQuery(this).data('star-rating', (data = new Starrr(jQuery(this), option)));
                }
                if (typeof option === 'string') {
                    return data[option].apply(data, args);
                }
            });
        }
    });
})(window.jQuery, window);

jQuery(function () {
    return jQuery(".starrr").starrr();
});

jQuery(document).ready(function () {

    jQuery('#stars').on('starrr:change', function (e, value) {
        jQuery('#count').html(value);
    });


    jQuery('#stars-existing').on('starrr:change', function (e, value) {
        jQuery('#count-existing').html(value);
    });

});
/** ******  /star rating  *********************** **/
/** ******  table  *********************** **/
jQuery('table input').on('ifChecked', function () {
    check_state = '';
    jQuery(this).parent().parent().parent().addClass('selected');
    countChecked();
});
jQuery('table input').on('ifUnchecked', function () {
    check_state = '';
    jQuery(this).parent().parent().parent().removeClass('selected');
    countChecked();
});

var check_state = '';
jQuery('.bulk_action input').on('ifChecked', function () {
    check_state = '';
    jQuery(this).parent().parent().parent().addClass('selected');
    countChecked();
});
jQuery('.bulk_action input').on('ifUnchecked', function () {
    check_state = '';
    jQuery(this).parent().parent().parent().removeClass('selected');
    countChecked();
});
jQuery('.bulk_action input#check-all').on('ifChecked', function () {
    check_state = 'check_all';
    countChecked();
});
jQuery('.bulk_action input#check-all').on('ifUnchecked', function () {
    check_state = 'uncheck_all';
    countChecked();
});

function countChecked() {
        if (check_state == 'check_all') {
            jQuery(".bulk_action input[name='table_records']").iCheck('check');
        }
        if (check_state == 'uncheck_all') {
            jQuery(".bulk_action input[name='table_records']").iCheck('uncheck');
        }
        var n = jQuery(".bulk_action input[name='table_records']:checked").length;
        if (n > 0) {
            jQuery('.column-title').hide();
            jQuery('.bulk-actions').show();
            jQuery('.action-cnt').html(n + ' Records Selected');
        } else {
            jQuery('.column-title').show();
            jQuery('.bulk-actions').hide();
        }
    }
    /** ******  /table  *********************** **/
    /** ******    *********************** **/
    /** ******    *********************** **/
    /** ******    *********************** **/
    /** ******    *********************** **/
    /** ******    *********************** **/
    /** ******    *********************** **/
    /** ******  Accordion  *********************** **/

jQuery(function () {
    jQuery(".expand").on("click", function () {
        jQuery(this).next().slideToggle(200);
        jQueryexpand = jQuery(this).find(">:first-child");

        if (jQueryexpand.text() == "+") {
            jQueryexpand.text("-");
        } else {
            jQueryexpand.text("+");
        }
    });
});

/** ******  Accordion  *********************** **/
/** ******  scrollview  *********************** **/
jQuery(document).ready(function () {
  
            /*jQuery(".scroll-view").niceScroll({
                touchbehavior: true,
                cursorcolor: "rgba(42, 63, 84, 0.35)"
            });*/

});
/** ******  /scrollview  *********************** **/