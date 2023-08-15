{% set Count = count(tags) %}
<div class="uk-section uk-section-small uk-padding-remove-top uk-margin-remove-top">
    <ul class="tags">
        {% for tag in tags %}
            {% if tag['tag'][0] is not empty %}
                <li class="tag"><a href="/search?tag={{ tag['tag'][0]['slug'] }}" class="tag__link"><span style="font-size:{{ tag['font-size'] }}pt;">{{ tag['tag'][0]['title'] }}</span></a></li>
            {% endif %}
        {% endfor %}
    </ul>
    <p>Klik <a href="/tag/overview">hier</a> voor alle tags</p>
</div>