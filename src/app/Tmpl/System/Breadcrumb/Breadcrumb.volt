{% set Counter = 1 %}
<ul class="uk-breadcrumb uk-flex-left uk-margin-remove" itemscope="" itemtype="https://schema.org/BreadcrumbList">
    {% for breadcrumb in breadcrumbs %}
        <li itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem">
            {% if breadcrumb['link'] is not empty %}
                <a itemprop="item" href="{{ breadcrumb['link'] }}">
                    <span itemprop="name">{{ html_entity_decode(breadcrumb['title']) }}</span>
                </a>
                <meta itemprop="position" content="{{ Counter }}">
            {% else %}
                <span itemprop="name">{{ html_entity_decode(breadcrumb['title']) }}</span>
                <meta itemprop="position" content="{{ Counter }}">
            {% endif %}
        </li>
        {% set Counter = Counter + 1 %}
    {% endfor %}
</ul>