<div class="uk-container">
    <article class="uk-article">
        <h1 class="uk-article-title">
            {{ item.title }}
        </h1>

        {% if item.summary is not empty %}
            <div class="post-summary uk-text-lead uk-margin">
                {{ item.summary }}
            </div>
        {% endif %}

        <p>Het formulier is succesvol verzonden.</p>
    </article>
</div>
