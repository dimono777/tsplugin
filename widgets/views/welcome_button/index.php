<?php
/**
 * @var $authLinkText string
 * @var string $regLink
 */
?>
<div class="log-in active">
    <?php echo TS_Functions::getAuthorisationLinkHtml($authLinkText); ?>
</div>
<div class="register">
    <?php echo $regLink; ?>
</div>