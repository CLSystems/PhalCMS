<!DOCTYPE html>
<html lang="{{ _('locale.code') }}"
      dir="{{ _('locale.direction') }}"
      data-uri-root="{{ constant('DOMAIN') }}"
      data-uri-home="{{ home() ? 'true' : 'false' }}"
      data-uri-base="{{ helper('Uri::getBaseUriPrefix') }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="robots" content="noindex, nofollow"/>
    <title>{{ get_title() }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ constant('DOMAIN') ~ '/assets/images/icon-sale-shopping-bag.png' }}"/>
    <link rel="stylesheet" type="text/css" href="{{ constant('DOMAIN') ~ '/assets/css/uikit.min.css' }}"/>
    <link rel="stylesheet" type="text/css" href="{{ constant('DOMAIN') ~ '/assets/chosen/chosen.min.css' }}"/>

    <script src="{{ constant('DOMAIN') ~ '/assets/js/jquery-3.6.0/jquery.min.js' }}"></script>
    <script src="{{ constant('DOMAIN') ~ '/assets/js/uikit-3.15.22/uikit.min.js' }}"></script>
    <script src="{{ constant('DOMAIN') ~ '/assets/js/uikit-3.15.22/uikit-icons.min.js' }}"></script>
    <script src="{{ constant('DOMAIN') ~ '/assets/chosen/chosen.jquery.min.js' }}"></script>
    <script src="{{ constant('DOMAIN') ~ '/assets/js/core.js' }}"></script>
    {{ trigger('onAdminHead', ['System', 'Cms']) | j2nl }}
</head>
<body>
{# Hook before content #}
{{ trigger('onAdminBeforeContent', [], ['System', 'Cms']) | j2nl }}

{# Main content #}
{% block adminContent %}{% endblock %}

{# Hook after content #}
{{ trigger('onAdminAfterContent', [], ['System', 'Cms']) | j2nl }}

{# Footer content #}
{{ helper('Text::fetchJsData') }}
</body>
</html>