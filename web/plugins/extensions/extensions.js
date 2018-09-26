$.fn.extend({
    hasClasses: function (selectors) {
        var self = this;

        for (var i in selectors) {
            if (!$(self).hasClass(selectors[i])) {
                return false;
            }
        }

        return true;
    }
});
