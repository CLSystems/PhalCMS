<header class="uk-background-default" id="header" uk-sticky="show-on-up: true; animation: uk-animation-fade; media: @xs">
    <div class="uk-container">
        <nav id="navbar" uk-navbar="mode: click;">
            <div class="uk-navbar-left nav-overlay">
                <a class="uk-navbar-item uk-logo" href="{{ route('/') }}" title="Logo {{ siteName | escape }}">
                    <img class="align-left"
                         src="{{ constant('DOMAIN') ~ '/assets/images/phalcms-logo.jpg' }}"
                         width="55" height="65" alt="Logo {{ siteName | escape }}"
                    />
                </a>
                <a class="uk-navbar-item uk-padding-remove" href="{{ route('/') }}" title="{{ siteName | escape }} | {{ _('catch-phrase') }}">
                    <div class="uk-text-large uk-text-emphasis">
                        {{ siteName | escape }}
                        <div class="uk-navbar-subtitle uk-text-small uk-text-muted">
                            {{ _('catch-phrase') }}
                        </div>
                    </div>
                </a>
            </div>

            <div class="uk-navbar-right nav-overlay">

                {% set mainMenu = menu('MainMenu', 'Navbar') %}
                {% set mainMenuItems = menu('MainMenu') %}
                {% set itemsRaw = substr(mainMenuItems, 27, -5) %}
                {% if mainMenu is not empty %}
                    <div class="uk-inline">
                        <button aria-label="menu button" class="uk-button uk-button-text uk-navbar-item" type="button" data-uk-navbar-toggle-icon></button>
                        <div uk-drop>
                            <div class="uk-card uk-card-body uk-card-default uk-card-small">
                                <ul class="uk-nav">
                                    {{ itemsRaw }}
                                </ul>
                            </div>
                        </div>
                    </div>

                {% endif %}

                <a aria-label="search toggle" class="uk-navbar-toggle uk-visible@xs" uk-search-icon
                   uk-toggle="target: .nav-overlay; animation: uk-animation-fade" href="#"></a>

                {{ widget('TopB', 'Raw') }}

                {% set topBMenu = menu('TopB', 'Navbar') %}
                {% if topBMenu is not empty %}
                    <div class="top-b-menu">
                        {{ topBMenu }}
                    </div>
                {% endif %}
            </div>
            <div class="nav-overlay uk-navbar-left uk-flex-1" hidden>
                <div class="uk-navbar-item uk-width-expand">
                    <form class="uk-search uk-search-navbar uk-width-1-1" action="{{ route('search') }}" method="get">
                        <input class="uk-search-input" name="q" type="search"
                               value="{{ request.get('q', ['trim', 'string'], '') }}"
                               placeholder="{{ _('search-hint') | escape_attr }}">
                    </form>
                </div>
                <a aria-label="search close button" class="uk-navbar-toggle" uk-close
                   uk-toggle="target: .nav-overlay; animation: uk-animation-fade" href="#"></a>
            </div>
        </nav>
    </div>
</header>
