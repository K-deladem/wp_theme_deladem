(function($) {
    'use strict';

    function openSvgMediaFrame(onSelect) {
        var frame = wp.media({
            title: 'Choisir une icône SVG',
            button: { text: 'Utiliser cette icône' },
            library: { type: 'image/svg+xml' },
            multiple: false
        });
        frame.on('select', function() {
            var att = frame.state().get('selection').first().toJSON();
            onSelect(att.id, att.url);
        });
        frame.open();
    }

    // Project meta box — upload
    $(document).on('click', '.dlm-projet-icon-upload', function(e) {
        e.preventDefault();
        var $wrap = $(this).closest('.dlm-icon-field');
        openSvgMediaFrame(function(id, url) {
            $wrap.find('.dlm-icon-id').val(id);
            $wrap.find('.dlm-icon-preview').html('<img src="' + url + '" style="width:48px;height:48px;" alt="">');
            $wrap.find('.dlm-icon-remove').show();
        });
    });

    // Project meta box — remove
    $(document).on('click', '.dlm-icon-remove', function(e) {
        e.preventDefault();
        var $wrap = $(this).closest('.dlm-icon-field');
        $wrap.find('.dlm-icon-id').val('');
        $wrap.find('.dlm-icon-preview').html('');
        $(this).hide();
    });

    // Intérêts — upload (delegated for dynamic rows)
    $(document).on('click', '.dlm-interet-icon-upload', function(e) {
        e.preventDefault();
        var $row = $(this).closest('.dlm-interet-row');
        openSvgMediaFrame(function(id, url) {
            $row.find('.dlm-interet-icon-id').val(id);
            $row.find('.dlm-interet-icon-preview').html('<img src="' + url + '" style="width:32px;height:32px;" alt="">');
            $row.find('.dlm-interet-icon-remove').show();
        });
    });

    // Intérêts — remove
    $(document).on('click', '.dlm-interet-icon-remove', function(e) {
        e.preventDefault();
        var $row = $(this).closest('.dlm-interet-row');
        $row.find('.dlm-interet-icon-id').val('');
        $row.find('.dlm-interet-icon-preview').html('');
        $(this).hide();
    });

})(jQuery);
