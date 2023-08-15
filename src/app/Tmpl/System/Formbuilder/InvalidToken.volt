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

        <p>Dit is een ongeldig formulier.<br />
            Ga terug naar het formulier via de link in het menu bovenaan de website en probeer opnieuw.</p>
    </article>
</div>
