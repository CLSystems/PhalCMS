{% set paginate = paginator.paginate() %}

    {{ partial('Pagination/Pagination') }}
    <table class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider">
        <thead>
        <tr>
            <th class="uk-width-expand uk-text-nowrap">
                {{ partial('Grid/Sort', ['text': _('title'), 'column': 'title', 'activeOrder' : 'title']) }}
            </th>
        </tr>
        </thead>
        <tbody>
        {% for item in paginate.items %}
            <tr>
                <td>
                    <a class="uk-link-reset" href="/search?tag={{item.slug }}">
                        {{ item.title }}
                    <small>{{ item.slug }}</small>
                    </a>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>
    {{ partial('Pagination/Pagination') }}
    <input type="hidden" name="postAction"/>
    <input type="hidden" name="entityId"/>
    {{ helper('Form::tokenInput') }}

