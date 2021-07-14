<?php
/**
 * @var string $domainId
 * @var int $crmId
 * @var int $currencyPrecision
 */
/** JUST TO DECEIVE IDE  */
if (false) { ?>
<script>
<?php } ?>
document.addEventListener( "DOMContentLoaded", function() {
    listen_userinfo = function(c) {

        if (typeof c.av_withdrawal === 'undefined') {
            return ;
        }

        var availableBalance = (c.av_withdrawal > 0)
                ? c.av_withdrawal
                : 0;

        document.getElementById("balance_av").innerHTML = Number(availableBalance)
            .toLocaleString("en-US", {
                minimumFractionDigits: <?php echo $currencyPrecision; ?>,
                maximumFractionDigits: <?php echo $currencyPrecision; ?>
            });
    };
    SiteWithdrawal('withdrawal', '<?php echo $crmId; ?>', listen_userinfo);
});