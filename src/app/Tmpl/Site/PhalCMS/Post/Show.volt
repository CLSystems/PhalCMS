<div class="uk-container" itemscope itemtype="http://schema.org/Brand">
    <article class="uk-article">
        <div class="uk-text-left">
            {{ partial('Breadcrumb/Breadcrumb') }}
        </div>
        <h1 class="uk-article-title" itemprop="name">
            {{ html_entity_decode(post.t('title')) }}
        </h1>
        <p class="uk-article-meta">
            {{ _('posted-in') ~ ' ' }}
            <a href="{{ post.category.link }}">
                {{ post.category.t('title') }}
            </a>
            {{ ' | ' ~ helper('IconSvg::render', 'eye') ~ ' ' ~ helper('Text::plural', 'hits', post.hits, ['hits' : post.hits]) }}
        </p>

        {% set vouchers = post.vouchers() %}
        {% if vouchers is not empty %}
            <h2>{{ _('discounts-at') }} {{ html_entity_decode(post.t('title')) | escape_attr }}</h2>
            {{ partial('UcmItem/VouchersSlider') }}
        {% endif %}

        {% if post.parentId is 118 %}
        <h2>{{ _('info-about') }} {{ lcfirst(html_entity_decode(html_entity_decode(post.t('title')))) }}</h2>
        {% else %}
        <h2>{{ _('info-about') }} {{ html_entity_decode(html_entity_decode(post.t('title'))) }}</h2>
        {% endif %}
        {% set images = helper('Image::loadImage', post.t('image'), false) %}
        {% set rating = post.rating() %}
        {% if rating is not empty %}
            <script type="application/ld+json">
                {
                    "@context": "https://schema.org/",
                    "@type": "Organization",
                    "brand": {
                        "@type": "Brand",
                        "name": "{{ rating.merchantName | escape_attr }}"
                    },
                    "description": "{{ strip_tags(html_entity_decode(post.t('summary'))) }}",
                    "image": "{{ images[0].getUri() }}",
                    "name": "{{ post.t('title') | escape_attr }}",
                    "aggregateRating": {
                        "@type": "AggregateRating",
                        "ratingValue": "{{ rating.ratingValue }}",
                        "bestRating": "100",
                        "ratingCount": "{{ rating.ratingCount }}"
                    }
                }
            </script>
            <div class="post-rating uk-text-muted uk-margin">{{ rating.ratingCount }} {{ _('reviews') }}<br/>
                {{ _('score') }}: <a href="{{ rating.externalRatingUrl }}" target="_blank" rel="noreferrer noopener">
                    {{ rating.ratingValue }} {{ _('out-of') }} 100
                </a>
            </div>
        {% endif %}

        {% set summary = post.t('summary') | trim %}
        {% if summary is not empty %}
            <div class="post-summary uk-text-lead uk-margin">
                {{ strip_tags(html_entity_decode(summary)) }}
            </div>
        {% endif %}

        {% if images | length > 0 %}
            {% if images | length > 1 %}
                <div class="post-images">
                    <div class="uk-position-relative" uk-slideshow>
                        <ul class="uk-slideshow-items" uk-lightbox>
                            {% set thumbNav = '' %}
                            {% for i, image in images %}
                                {% set ratio = image.getRatio() %}
                                <li>
                                    <a href="{{ image.getUri() }}">
                                        <div style="--aspect-ratio: {{ ratio }}/1">
                                            <img data-src="{{ image.getUri() }}" alt="{{ post.t('title') | escape_attr }}" uk-img />
                                        </div>
                                    </a>
                                </li>
                                {% set thumbNav = thumbNav ~ '<li uk-slideshow-item="' ~ i ~ '"><a href="#"><img src="' ~ image.getResize(100) ~ '" width="100" alt=""></a></li>' %}
                            {% endfor %}
                        </ul>
                        <div class="uk-position-bottom-center uk-position-small">
                            <ul class="uk-thumbnav">
                                {{ thumbNav }}
                            </ul>
                        </div>
                    </div>
                </div>
            {% else %}
                {% set ratio = images[0].getRatio() %}
                <div class="post-image" uk-lightbox>
                    <div style="--aspect-ratio: {{ ratio }}; --size: 100%; width: var(--size); height: calc(var(--size) / var(--aspect-ratio));">
                        <a href="{{ images[0].getUri() }}">
                                <img itemprop="logo" data-src="{{ images[0].getUri() }}" alt="{{ post.t('title') | escape_attr }}" uk-img
                                     style="--aspect-ratio: {{ ratio }}; --size: 100%; width: var(--size); height: calc(var(--size) / var(--aspect-ratio));"/>
                        </a>
                    </div>
                </div>
            {% endif %}
        {% endif %}

        {# Description (fields) for this post #}
        <span itemprop="description">{{ partial('UcmItem/Description') }}</span>

        {% if post.parentId is 118 %}
        {% if brandlink is not empty %}
            <a href="{{ brandlink }}">{{ _('click-here-for-brand') }}</a>
        {% endif %}
        {% endif %}

    </article>

    {# Comments for this post #}
    {{ partial('Comment/Comment') }}
</div>