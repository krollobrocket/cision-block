;
(function ($) {
    const wrappers = $('.cision-feed-wrapper')
    wrappers.toArray().forEach(it => {
        const id = $(it).attr('id')
        const all = $(it).children('[data-regulatory]')
        const regulatory = all.filter((i, v) => !!$(v).data('regulatory'))
        const nonRegulatory = all.filter((i, v) => !!!$(v).data('regulatory'))
        const filters = $(it).find('.cision-feed-filters button')
        // const cookieName = 'cision-block-filter-' + id
        // const cookieValue = $.cookie(cookieName)
        // if (cookieValue) {
        //     switch (cookieValue) {
        //         case 'all':
        //             $(all).show()
        //             break
        //         case 'regulatory':
        //             $(regulatory).show()
        //             $(nonRegulatory).hide()
        //             break
        //         case 'none-regulatory':
        //             $(regulatory).hide()
        //             $(nonRegulatory).show()
        //             break
        //     }
        // }
        filters.toArray().forEach(it => {
            const cssClass = $(it).attr('class')
            switch (cssClass) {
                case 'all':
                    $(it).bind('click', null, () => $(all).show())
                    // $.cookie(cookieName, 'all')
                    break
                case 'regulatory':
                    $(it).bind('click', null, () => {
                        $(regulatory).show()
                        $(nonRegulatory).hide()
                    })
                    // $.cookie(cookieName, 'regulatory')
                    break
                case 'non-regulatory':
                    $(it).bind('click', null, () => {
                        $(regulatory).hide()
                        $(nonRegulatory).show()
                    })
                    // $.cookie(cookieName, 'non-regulatory')
                    break
            }
        })
    })
})(jQuery);
