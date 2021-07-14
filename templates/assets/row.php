<?php
/**
 * @var string $domain
 * @var array  $assetsIds
 */
?>
<section id="tradestrip">
    <?php foreach ($assetsIds as $assetId) { ?>
        <div class="item assetId_<?php echo $assetId ?>">
            <span class="assetName">ASSET</span> <span class="assetSellRate">1.000000</span>
            <span class="assetPercent">0.00%</span>
        </div>
    <?php } ?>
</section>

<script>
  document.addEventListener(
      'DOMContentLoaded', function() {
        WLConnections(
            'assetsRates',
            <?php echo json_encode($assetsIds) ?>,
            assetsRatesCallback
        );
      }
  )
</script>