!function (t, e) {
    e((function () {
        var n = e('#search');
        e('form', n).submit((function (t) {
            var n = e('input:first', e(this));
            n.val().trim().length < 1 && (t.preventDefault(), n.focus())
        }));
        e('.coupon').on('click', (function (n) {
            n.preventDefault();
            var i = e('.coupon').data('offer-id'),
                a = '/outlink/'.concat(i);
            document.location.pathname.startsWith('/ac/') && (a += '?tag=KenA'),
            t.open(window.atob(e('.coupon').data('url')));
            setTimeout((function () {
                document.location.replace(a)
            }), 200)
        }))
    }))
}(window, $);

