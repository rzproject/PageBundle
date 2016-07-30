jQuery(document).ready(function() {
    jQuery('html').removeClass('no-js');

    Page.shared_setup(document);

    jQuery(document).ajaxStart(function() {
        jQuery.blockUI({ message: '<div class="load-container"><div class="loader">Loading...</div></div>',
            css: {
                border: 'none',
                padding: '0px',
                width:   'auto',
                left:     '50%',
                backgroundColor: 'transparent',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity:.4,
                color: '#fff'
            }});
    });

    jQuery(document).ajaxComplete(function(){
        jQuery.unblockUI();
    });
});

var Page = {

    /**
     * This function must called when a ajax call is done, to ensure
     * retrieve html is properly setup
     *
     * @param subject
     */
    shared_setup: function(subject) {
        Page.log("[core|shared_setup] Register services on", subject);

        console.log(window.SONATA_CONFIG);
        Page.setup_select2(subject);
        Page.setup_icheck(subject);
    },

    setup_icheck: function(subject) {
        if (window.SONATA_CONFIG && window.SONATA_CONFIG.USE_ICHECK) {
            Page.log('[core|setup_icheck] configure iCheck on', subject);

            jQuery("input[type='checkbox']:not('label.btn>input'), input[type='radio']:not('label.btn>input')", subject).iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue'
            });
        }
    },

    setup_select2: function(subject) {
        if (window.SONATA_CONFIG && window.SONATA_CONFIG.USE_SELECT2 && window.Select2) {
            Page.log('[core|setup_select2] configure Select2 on', subject);

            jQuery('select:not([data-sonata-select2="false"])', subject).each(function() {
                var select            = jQuery(this);
                var allowClearEnabled = false;
                var popover           = select.data('popover');

                select.removeClass('form-control');

                if (select.find('option[value=""]').length || select.attr('data-sonata-select2-allow-clear')==='true') {
                    allowClearEnabled = true;
                } else if (select.attr('data-sonata-select2-allow-clear')==='false') {
                    allowClearEnabled = false;
                }

                select.select2({
                    width: function(){
                        return Page.get_select2_width(this.element);
                    },
                    dropdownAutoWidth: true,
                    minimumResultsForSearch: 10,
                    allowClear: allowClearEnabled
                });

                if (undefined !== popover) {
                    select
                        .select2('container')
                        .popover(popover.options)
                    ;
                }
            });
        }
    },
    /** Return the width for simple and sortable select2 element **/
    get_select2_width: function(element){
        var ereg = /width:(auto|(([-+]?([0-9]*\.)?[0-9]+)(px|em|ex|%|in|cm|mm|pt|pc)))/i;

        // this code is an adaptation of select2 code (initContainerWidth function)
        var style = element.attr('style');
        //console.log("main style", style);

        if (style !== undefined) {
            var attrs = style.split(';');

            for (i = 0, l = attrs.length; i < l; i = i + 1) {
                var matches = attrs[i].replace(/\s/g, '').match(ereg);
                if (matches !== null && matches.length >= 1)
                    return matches[1];
            }
        }

        style = element.css('width');
        if (style.indexOf("%") > 0) {
            return style;
        }

        return '100%';
    },
    /**
     * render log message
     * @param mixed
     */
    log: function() {
        var msg = '[Rz.Page] ' + Array.prototype.join.call(arguments,', ');
        if (window.console && window.console.log) {
            window.console.log(msg);
        } else if (window.opera && window.opera.postError) {
            window.opera.postError(msg);
        }
    }
}