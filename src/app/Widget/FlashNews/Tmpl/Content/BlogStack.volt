<div class="blog-stack">
    {% for post in posts %}
        <div class="uk-card uk-background-muted uk-grid-collapse uk-margin" uk-grid>
            {% set image = helper('Image::loadImage', post.t('image')) %}
            {% if image is not empty %}
                {% set ratio = image.getRatio() %}
                <div class="uk-card-media-left uk-cover-container uk-width-1-3">
                    <a class="uk-link-reset" href="{{ post.link }}" title="{{ post.t('title') | escape_attr }}">
                        <div style="--aspect-ratio: {{ ratio }}/1">
                            <img data-src="{{ image.getResize(300, 400) }}" alt="{{ post.t('title') | escape_attr }}" uk-img />
                        </div>
                        <!--<img data-src="{{ image.getResize(300, 120) }}" alt="{{ post.t('title') | escape_attr }}" uk-img/>
                        <canvas width="300" height="120"></canvas>-->
                    </a>
                </div>
            {% endif %}
            <div class="uk-width-2-3">
                <div class="uk-padding-small">
                    <h4 class="uk-h5 uk-margin-remove uk-text-truncate">
                        <a class="uk-link-reset" href="{{ post.link }}"
                           title="{{ post.t('title') | escape_attr }}">
                            {{ post.t('title') }}
                        </a>
                    </h4>
                    <p class="uk-margin-remove uk-text-meta uk-text-break">
                        {{ strip_tags(html_entity_decode(post.summary())) }}
                    </p>
                    <a href="{{ post.link }}"
                       class="uk-button uk-button-default uk-button-small uk-margin">
                        {{ _('read-more') }}
                    </a>
                </div>
            </div>
        </div>
    {% endfor %}
</div>
