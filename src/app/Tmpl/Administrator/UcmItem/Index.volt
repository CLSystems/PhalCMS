{% set paginate = paginator.paginate() %}
{% if 'raw' === dispatcher.getParam('format') %}
    {% set isMultilingual = false %}
{% endif %}
<form id="admin-list-form" action="{{ uri.getActive() }}" method="post">
    {{ searchTools }}
    {% if paginate.getTotalItems() %}
        {{ partial('Pagination/PaginationTop') }}
        <div class="uk-overflow-auto">
            <table class="uk-table uk-table-small uk-table-striped uk-table-hover uk-table-middle uk-table-divider uk-table-striped">
                <thead>
                <tr>
                    <th class="uk-table-shrink uk-visible@s">
                        <input class="uk-checkbox check-all" type="checkbox"/>
                    </th>
                    <th class="uk-table-shrink uk-text-nowrap">
                        {{ partial('Grid/Sort', ['text': _('state'), 'column': 'state', 'activeOrder' : activeOrder]) }}
                    </th>

                    <th class="uk-table-shrink uk-text-nowrap">
                        {{ partial('Grid/Sort', ['text': _('logo'), 'column': 'image', 'activeOrder' : activeOrder]) }}
                    </th>

                    <th class="uk-table-expand uk-text-nowrap">
                        {{ partial('Grid/Sort', ['text': _('title'), 'column': 'title', 'activeOrder' : activeOrder]) }}
                    </th>

                    {{ trigger('onAfterUcmHeadTitle') | j2nl }}

                    <th class="uk-table-expand uk-text-nowrap">
                        {{ partial('Grid/Sort', ['text': _('URL'), 'column': 'route', 'activeOrder' : activeOrder]) }}
                    </th>

                    <th class="uk-table-shrink uk-text-nowrap">
                        {{ partial('Grid/Sort', ['text': _('Hits'), 'column': 'hits', 'activeOrder' : activeOrder]) }}
                    </th>

{#                    <th class="uk-table-shrink uk-text-nowrap">#}
{#                        {{ partial('Grid/Sort', ['text': _('Source'), 'column': 'source', 'activeOrder' : activeOrder]) }}#}
{#                    </th>#}

{#                    <th class="uk-table-shrink uk-text-nowrap">#}
{#                        {{ partial('Grid/Sort', ['text': _('Ext URL'), 'column': 'prefUrl', 'activeOrder' : activeOrder]) }}#}
{#                    </th>#}

                    <th class="uk-table-shrink uk-text-nowrap">
                        {{ partial('Grid/Sort', ['text': _('created-at'), 'column': 'createdAt', 'activeOrder' : activeOrder]) }}
                    </th>

                    <th class="uk-table-shrink uk-text-nowrap uk-visible@m">
                        {{ partial('Grid/Sort', ['text': _('ID'), 'column': 'id', 'activeOrder' : activeOrder]) }}
                    </th>
                </tr>
                </thead>
                <tbody>
                {% for item in paginate.getItems() %}
                    {% set title = item.title %}
                    <tr data-level="{{ item.level }}" data-id="{{ item.id }}"
                        data-parent-id="{{ item.parentId }}"
                        data-title="{{ title | escape_attr }}">
                        <td class="uk-visible@s">
                            <input class="uk-checkbox cid" type="checkbox" name="cid[]"
                                   value="{{ item.id }}"/>
                        </td>
                        <td class="uk-text-nowrap">
                            {{ partial('Grid/Status', ['item': item]) }}
                        </td>

                        <td class="uk-text-nowrap uk-text-meta uk-visible@m">
                            {% set image = helper('Image::loadImage', item.t('image')) %}
                            {% if image is not empty %}
                                <img src="{{ image.getResize(50, 50) }}" width="50" height="50" />
                            {% else %}
                                <img src="image_not_found.png" width="50" height="50" />
                            {% endif %}
                        </td>

                        <td class="ucm-item-title uk-text-small"{{ item.route ? ' title="' ~ item.description | escape_attr ~ '" uk-tooltip' : '' }}>
                            {% if item.isCheckedIn() %}
                                {{ partial('Grid/CheckedIn', ['item': item, 'title': title]) }}
                            {% else %}
                                <div class="uk-flex uk-flex-left">
                                    <div class="uk-flex-wrap">
                                        <div>
                                            <a class="uk-link-reset" href="{{ uri.routeTo('edit/' ~ item.id) }}">
                                            {{ html_entity_decode(title) }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            {% endif %}
                        </td>

                        {{ trigger('onAfterUcmBodyTitle', [item]) | j2nl }}

                        <td class="ucm-item-title uk-text-small">
                            <div class="uk-flex uk-flex-wrap">
                                <div>
                                    <a class="uk-link-reset" href="{{ uri.routeTo('edit/' ~ item.id) }}">
                                        {{ item.route }}
                                    </a>
                                </div>
                            </div>
                        </td>

                        <td class="uk-text-nowrap uk-text-meta uk-visible@m">
                            {{ item.hits }}
                        </td>

{#                        <td class="uk-text-nowrap uk-text-meta uk-visible@m">#}
{#                            {{ item.source.name }}#}
{#                        </td>#}

{#                        <td class="ucm-item-title uk-text-nowrap uk-text-meta uk-visible@m"{{ item.prefUrl ? ' title="' ~ item.prefUrl | escape_attr ~ '" uk-tooltip' : '' }}>#}
{#                            {% if item.prefUrl is not empty %}<a href="{{ item.prefUrl }}" target="_blank" rel="noreferrer noopener"><span uk-icon="icon: check"></span></a>{% endif %}#}
{#                        </td>#}

                        <td class="uk-text-nowrap uk-text-meta">
                            {{ helper('Date::relative', item.createdAt) }}
                        </td>

                        <td class="uk-text-nowrap uk-text-meta uk-visible@m">
                            {{ item.id }}
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
        {{ partial('Pagination/Pagination') }}
    {% else %}
        <div class="uk-alert uk-alert-warning">
            {{ _('no-items-found') }}
        </div>
    {% endif %}
    <input type="hidden" name="postAction"/>
    <input type="hidden" name="entityId"/>
    {{ helper('Form::tokenInput') }}
</form>
