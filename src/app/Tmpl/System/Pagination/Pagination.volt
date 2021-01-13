{% set paginate = paginator.paginate() %}
{% set k = ((paginate.current + 2 > paginate.last) ? paginate.last - 2 : ((paginate.current - 2 < 1) ? 3 : paginate.current)) %}
{% if admin() and uri is defined %}
    {% set prefix = 'index/?page=' %}
{% else %}
    {% set prefix = '/?page=' %}
    {% set uri = helper('Uri::getActive') %}
{% endif %}

{% if paginate.last > 1 %}
    <nav class="uk-margin">
        <ul class="uk-pagination uk-flex-center">

            {% if paginate.current >= 2 %}
                <li>
                    <a href="{{ uri.routeTo(prefix ~ paginate.first) }}">
                        <span uk-pagination-previous></span><span uk-pagination-previous></span>
                    </a>
                </li>
                <li>
                    <a href="{{ uri.routeTo(prefix ~ paginate.previous) }}">
                        <span uk-pagination-previous></span>
                    </a>
                </li>
            {% endif %}

            {% set j = -2 %}
            {% if paginate.current + j > paginate.first %}
                <li class="uk-disabled"><span>...</span></li>
            {% endif %}
            {% for i in j..2 %}
                {% if k+i == paginate.current %}
                    <li class="uk-active"><span><strong>{{ paginate.current }}</strong></span></li>
                {% else %}
                    <li>
                        <a href="{{ uri.routeTo(prefix ~ (k+i)) }}">{{ k+i }}</a>
                    </li>
                {% endif %}
            {% endfor %}
            {% if paginate.current - j < paginate.last %}
                <li class="uk-disabled"><span>...</span></li>
            {% endif %}

            {% if paginate.current + 2 <= paginate.last %}
                <li>
                    <a href="{{ uri.routeTo(prefix ~ paginate.next) }}">
                        <span uk-pagination-next></span>
                    </a>
                </li>
                <li>
                    <a href="{{ uri.routeTo(prefix ~ paginate.last) }}">
                        <span uk-pagination-next></span><span uk-pagination-next></span>
                    </a>
                </li>
            {% endif %}

        </ul>
    </nav>
{% endif %}
