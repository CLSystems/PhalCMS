<div class="widget-item3 widget-vouchers">
    <div class="slider-vouchers">
        <div uk-slider="autoplay: true; autoplay-interval: 3000; pause-on-hover: true">
            <div class="uk-slider-container uk-position-relative uk-visible-toggle">
                <ul class="uk-slider-items uk-grid uk-grid-small uk-child-width-1-2@m uk-child-width-1-1@xs">
                    {% for voucher in vouchers %}
                        <li>
                            <div class="uk-card uk-background-muted uk-grid-collapse" uk-grid>
                                {% set image = helper('Image::loadImage', voucher.t('image')) %}
                                {% if image is not empty %}
                                    {% set ratio = image.getRatio() %}
                                    <div class="uk-card-media-left uk-cover-container uk-width-1-3">
                                        <a class="uk-link-reset"
                                           href="{{ voucher.prefUrl }}"
                                           target="_blank" rel="noreferrer noopener"
                                           title="{{ _('click-here-for-discount-at') }} {{ post.t('title') | escape_attr }}">
                                            <div style="--aspect-ratio: {{ ratio }}/1">
                                                <img data-src="{{ image.getResize(300, 400) }}" alt="{{ voucher.t('title') | escape_attr }}" uk-img />
                                            </div>
                                        </a>
                                    </div>
                                {% endif %}

                                <div class="uk-width-2-3">
                                    <div class="uk-padding-small">
                                        <h3 class="uk-h5 uk-margin-remove uk-text-truncate">
                                            <a class="uk-link-reset"
                                               href="{{ voucher.prefUrl }}"
                                               target="_blank" rel="noreferrer noopener"
                                               title="{{ _('click-here-for-discount-at') }} {{ post.t('title') | escape_attr }}">
                                                {{ html_entity_decode(voucher.t('title')) }}
                                            </a>
                                        </h3>
                                        <p class="uk-margin-remove uk-text-meta uk-text-truncate">
                                            {{ voucher.summary() }}<br/>
                                            <a href="{{ voucher.prefUrl }}"
                                               target="_blank" rel="noreferrer noopener"
                                               class="uk-button uk-button-default uk-button-small uk-margin"
                                               title="{{ _('click-here-for-discount-at') }} {{ post.t('title') | escape_attr }}">
                                                {{ _('discount-code') }}
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </li>
                    {% endfor %}
                </ul>
                <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#"
                   uk-slidenav-previous
                   uk-slider-item="previous"></a>
                <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#"
                   uk-slidenav-next
                   uk-slider-item="next"></a>
            </div>
        </div>
    </div>
</div>