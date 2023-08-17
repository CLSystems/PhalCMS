<footer class="uk-section uk-section-small uk-section-muted" id="footer">

    <div class="uk-container">
        <div class="uk-grid-small uk-grid-match uk-child-width-1-1@xs uk-child-width-1-2@s uk-child-width-1-3@l" uk-grid>
            <!-- Trending -->
            <div>
                <div class="uk-card uk-card-small uk-card-default uk-card-body uk-flex uk-flex-column uk-flex-top uk-flex-left">
                    <h5>Trending</h5>
                    <table cellspacing="5" cellpadding="0">
                        <tbody>
                        <tr>
                            <td>
                                <a href="#" title="Display">Display</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="#" title="Trending">Trending</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="#" title="Links">Links</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="#" title="Here">Here</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Tips -->
            <div>
                <div class="uk-card uk-card-small uk-card-default uk-card-body uk-flex uk-flex-column uk-flex-top uk-flex-left">
                    <h5>Tips</h5>
                    <table cellspacing="5" cellpadding="0">
                        <tbody>
                        <tr>
                            <td>
                                <a href="#" title="Display">Display</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="#" title="Tips">Tips</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="#" title="Links">Links</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="#" title="Here">Here</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Social media -->
            <div>
                <div class="uk-card uk-card-small uk-card-default uk-card-body uk-flex uk-flex-column uk-flex-top uk-flex-left">
                    <h5>Social media</h5>
                    <table cellspacing="5" cellpadding="5">
                        <tbody>
                        <tr>
                            <td><span uk-icon="icon: linkedin"></span> <a href="https://www.linkedin.com/company/clsystems/" target="_blank" rel="noreferrer noopener">LinkedIn</a></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="uk-container">
        <div class="uk-padding-small">
            &copy; 2010-{{ date('Y') }} {{ siteName }} by <a href="https://clsystems.nl" target="_blank" rel="noreferrer noopener">CLSystems</a> {{ widget('Footer') }}
        </div>
    </div>
</footer>

<div class="uk-position-fixed uk-position-bottom-right uk-position-medium">
    <a aria-label="scroll to top" href="#" uk-totop uk-scroll></a>
</div>

<div id="wrapfabtest">
    <div class="adBanner">
    </div>
</div>

<style>
    .adBanner {
        background-color: transparent;
        height: 1px;
        width: 1px;
    }
    div#wrapfabtest {
        position: sticky;
        bottom: 0;
        width: 100%;
        background-color: transparent;
        color: #fff;
        text-align: center;
    }
</style>

<script>
    $(document).ready(function(){
        let selector = $("#wrapfabtest");
        if(selector.height() > 0) {
//					alert('No AdBlock :)');
        } else {
            selector.height(60);
            selector.css('background-color', '#552424');
            selector.css('padding-top', '10px');
            selector.html('AdBlocker aangetroffen! Deze website werkt het beste met AdBlockers uitgeschakeld.');
        }
    });
</script>

<!-- Cookies
<div id="cookieconsent"></div>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.css" />
<script src="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.js"></script>
<script>
    window.cookieconsent.initialise({
        container: document.getElementById("cookieconsent"),
        "palette": {
            "popup": {
                "background": "#000"
            },
            "button": {
                "background": "#f1d600"
            }
        },
        revokable: true,
        onStatusChange: function(status) {
            console.log(this.hasConsented() ?
                'enable cookies' : 'disable cookies');
        },
        "position": "bottom-right",
        "theme": "edgeless",
        "domain": "https://korting-en-acties.nl/",
        "secure": true,
        // "type": "opt-out",
        "content": {
            "header": 'Cookies used on this website!',
            "message": 'This website uses cookies to improve your experience.',
            "dismiss": 'Got it!',
            "allow": 'Allow cookies',
            "deny": 'Decline',
            "link": 'Learn more',
            "href": 'https://www.cookiesandyou.com',
            "close": '&#x274c;',
            "policy": 'Cookie Policy',
            "target": '_blank',
        }
    });

</script>
-->
<!-- Matomo -->
<script defer type="text/javascript">
    var _paq = window._paq = window._paq || [];
    /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
    _paq.push(['trackPageView']);
    _paq.push(['enableLinkTracking']);
    (function() {
        var u="//clstats.net/";
        _paq.push(['setTrackerUrl', u+'matomo.php']);
        _paq.push(['setSiteId', '16']);
        var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
        g.type='text/javascript'; g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
    })();
</script>
<!-- End Matomo Code -->

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-QGHHE4K5JH"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-QGHHE4K5JH');
</script>
