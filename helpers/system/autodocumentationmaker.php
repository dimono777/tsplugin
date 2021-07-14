<?php
namespace tradersoft\helpers\system;

use tradersoft\helpers\Config;

class AutoDocumentationMaker
{
    /**
     * print short codes doc in table
     */
    public static function printNativeShortCodeDoc()
    {
        $shortCodesDocs = Config::get('short_codes', []);

        $template = "<div class=\"table-row table-head\"><div class=\"table-empty\"></div><div class=\"shortcodes-name\">Name</div><div class=\"shortcodes-description\">Description</div><div class=\"shortcodes-example\">Example</div><div class=\"table-empty\"></div></div>";
        foreach ($shortCodesDocs as $shortCode => $doc) {
            $template .= "<div class=\"table-row\">
<div class=\"table-empty\"></div>
<div class=\"shortcodes-name\">$shortCode</div><div class=\"shortcodes-description\">{$doc['description']}</div>
<div class=\"shortcodes-example\">{$doc['example']}</div>
<div class=\"table-empty\"></div>
</div>";
        }

        echo $template;
    }

    /**
     * Conclusion short code description
     */
    public static function  printPageShortCodeDoc()
    {
        $keys = PageKey::getPagesKey();
        $template = "<div class=\"table-row table-head\"><div class=\"table-empty\"></div><div class=\"shortcodes-name\">Name</div><div class=\"shortcodes-description\">Description</div><div class=\"shortcodes-example\">Example</div><div class=\"table-empty\"></div></div>";

        foreach ($keys as $key => $doc) {
            $template .= "<div class=\"table-row\">
<div class=\"table-empty\"></div>
<div class=\"shortcodes-name\">$key</div>
<div class=\"shortcodes-description\">{$doc['description']}</div>
<div class=\"shortcodes-example\">{$doc['example']}</div>
<div class=\"table-empty\"></div>
</div>";
        }

        echo $template;
    }
}