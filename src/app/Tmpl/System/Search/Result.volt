{% if paginator is defined and paginator is not empty %}
    {% set paginater = paginator.paginate() %}
    {% set count = paginater.getTotalItems() %}
{% else %}
    {% set count = 0 %}
{% endif %}

{% if (count > 0) %}
    <div class="uk-container">
        {{ partial('Pagination/Pagination') }}
        <div class="uk-grid-small uk-grid-match uk-child-width-1-1@xs uk-child-width-1-2@m" uk-grid>
            {% for item in paginater.getItems() %}
            <div>
                <div class="uk-card uk-card-small uk-card-default uk-card-body uk-flex uk-flex-column uk-flex-top uk-flex-left">
                        <div class="result-title">
                            <a class="uk-text-lead" href="{{ route(item.t('route')) }}">
                                {{ item.t('title') }}
                            </a>
                        </div>
                        <div class="result-summary">
                            <div class="uk-grid-small" uk-grid>
                                {% set image = helper('Image::loadImage', item.t('image')) %}
                                {% if image %}
                                    {% set ratio = image.getRatio() %}
                                    <div class="uk-width-auto">
                                        <a class="uk-link-reset" href="{{ route(item.t('route')) }}">
                                            <img src="{{ image.getResize(85, 85) }}" alt="{{ item.title | escape_attr }}" />
                                        </a>
                                    </div>
                                {% endif %}
                                <div class="{{ image ? 'uk-width-expand' : 'uk-width-1-1' }}">
                                    <p class="uk-text-meta">
                                        {{ helper('StringHelper::truncate', strip_tags(html_entity_decode(item.summary())), 160) }}<br/>
                                        <a href="{{ item.link }}" class="uk-button uk-button-default uk-button-small uk-margin">
                                            {{ _('read-more') }}
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            {% endfor %}
        </div>
    </div>

    {{ partial('Pagination/Pagination') }}
{% else %}
    <div class="uk-alert uk-alert-warning">
        {{ _('no-results') }}
    </div>
{% endif %}