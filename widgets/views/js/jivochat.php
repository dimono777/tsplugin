<?php
/**
 * @var string $crmHashId
 * @var string $userName
 * @var string $email
 * @var string $phone
 */

/** JUST TO DECEIVE IDE  */
if (false) { ?>
    <script>
<?php } ?>
function jivo_onOpen() {
    jivo_api.setContactInfo({
        "name": "<?php echo $userName; ?>",
        "email": "<?php //echo $email; ?>",
        "phone": "<?php //echo $phone; ?>",
        "description": ""
    });

    jivo_api.setUserToken('<?php echo $crmHashId; ?>');
}