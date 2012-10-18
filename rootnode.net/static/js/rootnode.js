!function ($) {

    $(function(){

        var $window = $(window);
        var $window_width = 0;

        // Disable certain links in docs
        $('section [href=#]').click(function (e) {
            e.preventDefault()
        })

        // side bar
        $('.bs-docs-sidenav').affix({
            offset: {
                top: function () { return $window.width() <= 980 ? 290 : 210 }
                , bottom: 270
            }
        })

        // make code pretty
        window.prettyPrint && prettyPrint()

        setInterval(function() {
            if($window_width != $window.width()) {
                $window_width = $window.width();
                if($window_width < 768) {
                    $('.icon-chevron-left').removeClass('icon-chevron-left').addClass('icon-chevron-right');
                    $('.bs-docs-sidebar').parent().prepend($('.bs-docs-sidebar'));
                } else {
                    $('.icon-chevron-right').addClass('icon-chevron-left').removeClass('icon-chevron-right');
                    $('.bs-docs-sidebar').parent().append($('.bs-docs-sidebar'));
                }
            }
        }, 100);
    })

}(window.jQuery)
