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

    <link rel="shortcut icon" type="image/x-icon" href="{{ constant('DOMAIN') ~ '/assets/images/favicon.ico' }}"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/uikit@3.3.2/dist/css/uikit.min.css"/>
    <link rel="stylesheet" type="text/css" href="{{ constant('DOMAIN') ~ '/assets/chosen/chosen.min.css' }}"/>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.3.2/dist/js/uikit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.3.2/dist/js/uikit-icons.min.js"></script>
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
