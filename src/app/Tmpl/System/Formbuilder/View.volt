<div class="uk-container">
    {{ flashSession.output() }}
    <article class="uk-article">
        <h1 class="uk-article-title">
            {{ item.title }}
        </h1>

        {% if item.summary is not empty %}
            <div class="post-summary uk-text-lead uk-margin">
                {{ item.summary }}
            </div>
        {% endif %}

        <form method="post">
            <p>Velden met een <span style="color:red;">*</span> zijn verplicht.</p>
            <div id="render-wrap"></div>
            <div class="h-captcha" data-sitekey="70ae5a76-0192-4053-a3ce-f4a3390098cc"></div>
            <button type="submit" class="uk-button uk-button-default uk-button-small uk-margin">Versturen</button>
            {{ helper('Form::tokenInput') }}
        </form>
    </article>
</div>

<script src='https://www.hCaptcha.com/1/api.js?hl=nl' async defer></script>
<script type="application/javascript" src="https://korting-en-acties.nl/assets/editors/formbuilder/form-render.min.js"></script>
<script type="application/javascript">
    const container = document.getElementById('render-wrap');
    const formData = {{ item.description }};
    const options = {
        i18n: {
            locale: 'nl-NL',
            location: 'https://korting-en-acties.nl/assets/editors/formbuilder'
        }
    };
    const formRenderOpts = {
        container,
        formData,
        dataType: 'json',
        options
    };

    $(container).formRender(formRenderOpts);
</script>