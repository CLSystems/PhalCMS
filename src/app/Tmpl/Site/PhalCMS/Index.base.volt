<!DOCTYPE html>
<html lang="{{ _('locale.code') }}"
      dir="{{ _('locale.direction') }}"
      data-uri-home="{{ home() ? 'true' : 'false' }}"
      data-uri-root="{{ constant('DOMAIN') }}"
      data-uri-base="{{ helper('Uri::getBaseUriPrefix') }}">
<head>
    <meta charset="utf-8"/>
    <meta name="Cache-Control" content="public; max-age=262800">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#4285f4">
    <link rel="manifest" href="{{ constant('DOMAIN') ~ '/manifest.json' }}">
    <!-- VDS1 -->
    {% if metadata is defined %}
        {% if metadata.metaKeys is not empty %}
            <meta name="keywords" content="{{ metadata.metaKeys }}"/>
        {% endif %}
        {% if metadata.metaDesc is not empty %}
            <meta name="description" content="{{ metadata.metaDesc }}"/>
        {% endif %}
        {% if metadata.contentRights is not empty %}
            <meta name="rights" content="{{ metadata.contentRights }}"/>
        {% endif %}
        {% if metadata.metaRobots is not empty %}
            <meta name="robots" content="{{ metadata.metaRobots }}"/>
        {% endif %}

        {% if metadata.metaTitle is not empty %}
            {% if metadata.metaTitle == siteName %}
                <title>{{ _('catch-phrase') }} | {{ siteName | escape }}</title>
            {% else %}
                <title>{{ metadata.metaTitle }} | {{ helper('Date::getMonthYear') }} | {{ siteName | escape }}</title>
            {% endif %}
        {% endif %}

    {% endif %}

    <link rel="canonical" href="{{ helper('Uri::fromServer') }}" />

    {% block head %}{% endblock %}
    <link rel="shortcut icon" href="{{ constant('DOMAIN') ~ '/assets/images/phalcms-logo.jpg.webp' }}"/>
    <link rel="apple-touch-icon" href="{{ constant('DOMAIN') ~ '/assets/images/phalcms-logo.jpg.webp' }}"/>

    <link rel="stylesheet" type="text/css" href="{{ constant('DOMAIN') ~ '/assets/css/uikit.min.css' }}"/>
    <link rel="stylesheet" type="text/css" href="{{ constant('DOMAIN') ~ '/assets/css/custom.css' }}?{{ date("ymdH") }}"/>

    {{ trigger('onSiteHead', ['System', 'Cms']) | j2nl }}

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "PhalCMS",
            "url": "https://phalcms.net",
            "description": "Content management made easy!",
            "image": "{{ constant('DOMAIN') ~ '/assets/images/phalcms-logo.jpg.webp' }}",
            "logo": "{{ constant('DOMAIN') ~ '/assets/images/phalcms-logo.jpg.webp' }}",
            "telephone": "0647794655",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "Molenweg 13",
                "addressLocality": "Putten",
                "addressRegion": "GLD",
                "postalCode": "3882AB",
                "addressCountry": "Nederland"
            }
        }
    </script>

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "name": "PhalCMS",
            "url": "https://phalcms.net",
            "potentialAction": {
                "@type": "SearchAction",
                "query-input": "required name=query",
                "target": "https://phalcms.net/search?q={query}"
            }
        }
    </script>
</head>
<body>
{# Hook before content #}
{{ trigger('onSiteBeforeContent', [], ['System', 'Cms']) | j2nl }}

{# Block before content #}
{% block siteBeforeContent %}{% endblock %}

{# Block main content #}
{% block siteContent %}{% endblock %}

{# Block after content #}
{% block siteAfterContent %}{% endblock %}

{# Hook after content #}
{{ trigger('onSiteAfterContent', [], ['System', 'Cms']) | j2nl }}

{% block afterBody %}{% endblock %}

{# Hook after render #}
{{ trigger('onSiteAfterRender', [], ['System', 'Cms']) | j2nl }}

<script defer src="{{ constant('DOMAIN') ~ '/assets/js/jquery-3.6.0/jquery.min.js' }}"></script>
<script defer src="{{ constant('DOMAIN') ~ '/assets/js/uikit-3.15.22/uikit.min.js' }}"></script>
<script defer src="{{ constant('DOMAIN') ~ '/assets/js/uikit-3.15.22/uikit-icons.min.js' }}"></script>

</body>
</html>