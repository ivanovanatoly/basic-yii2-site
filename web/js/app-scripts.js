$(document).ready(function() {
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });
    $('[data-toggle="popover"]').popover({
        trigger  : 'manual',
        html     : true,
        container: 'body'
    }).on("mouseenter", function() {
        var _this = this;
        $(this).popover("show");
        $(".popover").on("mouseleave", function() {
            $(_this).popover('hide');
        });
    }).on("mouseleave", function() {
        var _this = this;
        setTimeout(function() {
            if (!$(".popover:hover").length) {
                $(_this).popover("hide");
            }
        }, 300);
    });
    sam.hamburgerSidemenu.init();
    sam.helpVideo.init();
});

var sam = {
    hamburgerSidemenu: {
        'init': function() {
            $('.js-hamburger-sidemenu').click(function() {
                sam.hamburgerSidemenu.save(this);
            });
        },
        'save': function(el) {
            $.ajax({
                'url'     : $(el).data('url'),
                'type'    : 'POST',
                'dataType': 'json',
                'data'    : {
                    '_csrf'       : sam._csrf,
                    'UserSettings': {
                        'sidebar_menu': !$('#container').hasClass('mainnav-lg') ? 1 : 0
                    }
                }
            });
        }
    },
    helpVideo        : {
        'init': function() {
            $('.js-tournaments-help-video').click(function() {
                sam.helpVideo.save($(this).data('url'), {'tournaments_help_video': $(this).attr('aria-expanded') === 'false' ? 1 : 0})
            });
            $('.js-users-help-video').click(function() {
                sam.helpVideo.save($(this).data('url'), {'users_help_video': $(this).attr('aria-expanded') === 'false' ? 1 : 0})
            });
            $('.js-categories-help-video').click(function() {
                sam.helpVideo.save($(this).data('url'), {'categories_help_video': $(this).attr('aria-expanded') === 'false' ? 1 : 0})
            });
        },
        'save': function(url, settings) {
            $.ajax({
                'url'     : url,
                'type'    : 'POST',
                'dataType': 'json',
                'data'    : {
                    '_csrf'       : sam._csrf,
                    'UserSettings': settings
                }
            });
        }
    },
    locker           : {
        lock: function(el) {
            var lockerContent = $('<div>', {'class': 'js-locker-content'}).html($(el).html()).hide();
            $(el).html(lockerContent);
            if (!$(el).hasClass('btn-labeled')) {
                $(el).append(this.getLoadGif());
            } else {
                $(el).addClass('js-locker-locked');
            }
            if ($(el).data('lockerText')) {
                $(el).append($(el).data('lockerText'));
            }
            $(el).attr('disabled', 1);
            $(el).prop('disabled', true);
        },
        unlock: function(el) {
            var lockerContent = $(el).find('.js-locker-content').html();
            $(el).html(lockerContent);
            $(el).removeClass('js-locker-locked');
            $(el).removeAttr('disabled');
            $(el).prop('disabled', false);
        },
        getLoadGif: function() {
            return $('<i>', {'class': 'fa fa-refresh'}).css('paddingRight', 4);
        }
    }
};

function in_array(needle, haystack, strict) {
    var found = false, key, strict = !!strict;
    for (key in haystack) {
        if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
            found = true;
            break;
        }
    }

    return found;
}
