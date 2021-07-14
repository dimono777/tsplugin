<?php
$lang = TS_Functions::getCurrentLanguage();
$domain = TS_Functions::getMainDomain();
$platformHost = \tradersoft\helpers\Platform::getDomain();
$googleClientId = \tradersoft\components\GoogleAnalytics::getClientId();
/** JUST TO DECEIVE IDE  */
if (false) { ?>
    <script>
<?php } ?>
var GLOBAL = GLOBAL || {};
GLOBAL.language = '<?php echo $lang; ?>';
GLOBAL.platformURL = '<?php echo $platformHost; ?>/';
document.domain = '<?php echo $domain; ?>';

<?php if ($googleClientId) { ?>
GLOBAL.googleClientId = '<?php echo $googleClientId; ?>';
<?php } ?>