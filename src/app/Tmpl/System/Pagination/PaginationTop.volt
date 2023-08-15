{% set paginate = paginator.paginate() %}
{% set k = ((paginate.current + 2 > paginate.last) ? paginate.last - 2 : ((paginate.current - 2 < 1) ? 3 : paginate.current)) %}
{% set suffix = '' %}
{% if admin() and uri is defined %}
    {% set prefix = 'index/?page=' %}
{% else %}
    {% set prefix = '/?page=' %}
    {% set uri = helper('Uri::getActive') %}
    {% if uri.getQuery('q') is not empty %}
        {% set suffix = '&q=' ~ uri.getQuery('q') %}
    {% endif %}
    {% if uri.getQuery('tag') is not empty %}
        {% set suffix = '&tag=' ~ uri.getQuery('tag') %}
    {% endif %}
{% endif %}

{% if paginate.last > 1 %}
    <nav class="uk-margin">
        <ul class="uk-pagination uk-flex-center">

            {% if paginate.current >= 2 %}
                <li>
                    <a rel="nofollow" href="{{ uri.routeTo(prefix ~ paginate.first) ~ suffix }}">
                        <span uk-pagination-previous></span><span uk-pagination-previous></span>
                    </a>
                </li>
                <li>
                    <a rel="nofollow" href="{{ uri.routeTo(prefix ~ paginate.previous) ~ suffix }}">
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
                    {% if k+i > 0 %}
                        <li>
                            <a rel="nofollow" href="{{ uri.routeTo(prefix ~ (k+i)) ~ suffix }}">{{ k+i }}</a>
                        </li>
                    {% endif %}
                {% endif %}
            {% endfor %}
            {% if paginate.current - j < paginate.last %}
                <li class="uk-disabled"><span>...</span></li>
            {% endif %}

            {% if paginate.current + 2 <= paginate.last %}
                <li>
                    <a rel="nofollow" href="{{ uri.routeTo(prefix ~ paginate.next) ~ suffix }}">
                        <span uk-pagination-next></span>
                    </a>
                </li>
                <li>
                    <a rel="nofollow" href="{{ uri.routeTo(prefix ~ paginate.last) ~ suffix }}">
                        <span uk-pagination-next></span><span uk-pagination-next></span>
                    </a>
                </li>
            {% endif %}

        </ul>
    </nav>
{% endif %}