;
(function ($) {
    $(document).on('ready', () => {
        $(':checkbox[name=mark_regulatory]').on('change', function () {
            if (this.checked) {
                $('[name=regulatory_text],[name=non_regulatory_text]').prop('disabled', false)
            } else {
                $('[name=regulatory_text],[name=non_regulatory_text]').prop('disabled', true)
            }
        });
        $(':checkbox[name=show_filters]').on('change', function () {
            if (this.checked) {
                $('[name=filter_all_text],[name=filter_regulatory_text],[name=filter_non_regulatory_text]').prop('disabled', false)
            } else {
                $('[name=filter_all_text],[name=filter_regulatory_text],[name=filter_non_regulatory_text]').prop('disabled', true)
            }
        });
    });
})(jQuery);
