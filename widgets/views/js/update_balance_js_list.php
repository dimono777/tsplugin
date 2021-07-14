<?php
/**
 * @var int $crmHashId
 * @var int $currencyPrecision
 * @var int $currentFinanceInfoTypeId
 */
/** JUST TO DECEIVE IDE  */
if (false) { ?>
<script>
<?php } ?>
var GLOBAL = GLOBAL || {};
<?php if ($crmHashId) {  ?>
GLOBAL.crmHashId = <?php echo $crmHashId;?>;
<?php } ?>
GLOBAL.currencyPrecision = <?php echo (int) $currencyPrecision;?>;
GLOBAL.currentFinanceInfoTypeId = <?php echo (int) $currentFinanceInfoTypeId;?>;