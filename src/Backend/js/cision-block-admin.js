;
(function ($) {
    $(document).on('ready', () => {
        $(':checkbox[name=mark-regulatory]').on('change', function () {
            if (this.checked) {
                $('[name=regulatory-text],[name=non-regulatory-text]').prop('disabled', false)
            } else {
                $('[name=regulatory-text],[name=non-regulatory-text]').prop('disabled', true)
            }
        });
        $(':checkbox[name=show-filters]').on('change', function () {
            if (this.checked) {
                $('[name=filter-all-text],[name=filter-regulatory-text],[name=filter-non-regulatory-text]').prop('disabled', false)
            } else {
                $('[name=filter-all-text],[name=filter-regulatory-text],[name=filter-non-regulatory-text]').prop('disabled', true)
            }
        });
    });
})(jQuery);
