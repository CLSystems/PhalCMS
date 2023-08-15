<?php

namespace CLSystems\PhalCMS\Lib\Helper;

class IconSvg
{
    public static function render($name, $width = 20, $height = 20, $alt = '')
    {
        static $iconCss = false;

        if (strpos($name, '<') === 0) {
            return $name;
        }

        if (!$iconCss) {
            $iconCss = true;
            Asset::addFile('icon.css');
        }

        $icon = DOMAIN . '/assets/images/icons.svg';

        return <<<SVG
<svg class="icon-{$name}" width="{$width}" height="{$height}"><title>{$alt}</title><use xlink:href="{$icon}#icon-{$name}"></use></svg>
SVG;
    }
}
