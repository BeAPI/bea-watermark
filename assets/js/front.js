// Site script
(function () {
    "use strict";
    var watermark_template = jQuery('#tpl-watermark').html();

    // Do not make the watermark
    if (_.isEmpty(watermark_template)) {
        return;
    }

    jQuery("img[data-watermark]").each(function () {
        var el = jQuery(this),
            classes = [],
            width_img = el.width();

        if (el.hasClass('alignleft') || el.hasClass('alignnone')) {
            el.removeClass('alignleft');
            classes.push('alignleft');
        } else {
            if (el.hasClass('alignright')) {
                el.removeClass('alignright');
                classes.push('alignright');
            } else {
                if (el.hasClass('aligncenter')) {
                    el.removeClass('aligncenter');
                    classes.push('aligncenter');
                }
            }
        }

        if (width_img < 200) {
            classes.push(' img-watermark-small');
        }

        el.after(_.template(watermark_template, {
            watermark: el.data('watermark')
        })).prependTo(el.next('.img-watermark-holder').addClass(classes.join(' ')));

    });
})();