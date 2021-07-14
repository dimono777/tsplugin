<?php
/** @var $trader tradersoft\model\Trader */

use tradersoft\helpers\RecentWithdrawalsSettings;

$trader = TSInit::$app->trader;
$session = new \tradersoft\helpers\Session();

$traderWithdrawalModel = TSInit::$app->getVar('traderWithdrawalModel');
$fees = $traderWithdrawalModel->fees;
$requests = $traderWithdrawalModel->requests;
$statuses = $traderWithdrawalModel->statuses;
$canCancel = false;
$recentWithdrawalsViewType = TSInit::$app->getVar(RecentWithdrawalsSettings::SETTING_NAME);

$messages = TSInit::$app->getVar('messages');

$domain = TS_Functions::getMainDomain();

/** @var \tradersoft\helpers\Currency $currency */
$currency = TSInit::$app->getVar('currency');
?>

<style>
    .withdrawal-popup-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2002;
    }
    .box-modal-wrapper {
        position: relative;
        max-width: 500px;
        background-color: #fff;
        padding: 32px 40px;
        text-align:center;
    }
    .box-modal-close {
        position: absolute;
        right: 16px;
        top: 16px;
        width: 16px;
        height: 16px;
        opacity: 0.3;
        cursor:pointer;
    }
    .box-modal-close:hover {
        opacity: 1;
    }
    .box-modal-close:before, .box-modal-close:after {
        position: absolute;
        left: 8px;
        content: ' ';
        height: 16px;
        width: 2px;
        background-color: #333;
    }
    .box-modal-close:before {
        transform: rotate(45deg);
    }
    .box-modal-close:after {
        transform: rotate(-45deg);
    }

    .box-modal-title {
        font-size: 24px;
        font-weight:bolder;
        margin-bottom:20px;
    }
    .box-modal-description {
        font-size: 16px;
        margin-bottom:20px;
    }
    .button-cancel {
        font-size: 24px;
        line-height:50px;
        height:50px;
        background:#3cb868;
        color:#fff;
        text-transform:uppercase;
        text-align:center;
        display:block;
        width:100%;
        padding:0 10px;
        margin-top:40px;
    }
    .button-cancel:hover {
        opacity:0.8;
    }
    [v-cloak] {
        display:none;
    }
</style>

<div id="withdraw" class="withdrawal" :class="{'ajax-loader': showLoader }">
    <div class="row">
        <div class="col-lg-6">
            <div class="title"><?php echo \TS_Functions::__('Withdrawal') ?></div>
            <div class="withdrawal-block">
                <div class="withdrawal-founds">
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="title"><?php echo \TS_Functions::__('Amount balance:') ?></div>
                        </div>
                        <div class="col-lg-7 text-right">
                            <div class="founds">
                                <span><?php echo $currency->getSymbol() ?></span>
                                <span v-cloak>{{ balanceWithBonuses | numberStyle }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="title"><?php echo \TS_Functions::__('Available funds') ?></div>
                        </div>
                        <div class="col-lg-7 text-right">
                            <div class="founds">
                                <span class="green"><?php echo $currency->getSymbol() ?></span>
                                <span class="green" v-cloak>{{ balance | numberStyle }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $form = \tradersoft\helpers\Form::begin($traderWithdrawalModel, [
                        'enableClientValidation' => false,
                        'htmlOptions' => [
                            'class' => 'form',
                        ]
                ])?>
                <?php echo \tradersoft\helpers\Html::hiddenInput('tradersoft_submit', 'withdrawal'); ?>
                <div class="row amount">
                    <div class="col-lg-7">
                        <div class="withdrawal-amount">
                            <div class="title"><?php echo \TS_Functions::__('Amount:') ?></div>
                            <div class="amount-input">
                                <?php echo $form->field('amount', [
                                    'options'=> [
                                        'class' => "input-group form-row {{ amountError ? 'has-error' : '' }}",
                                    ],
                                    'template' => "{label}\n{input}<small class=\"error error-text-js\" v-show=\"amountError\" v-text=\"amountError + getAmountValueForError()\"></small>",
                                    ])->textInput([
                                        ':disabled' => 'disabled',
                                        'v-model' => 'amount',
                                ])?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5" v-if="showFeeBlock" v-cloak>
                        <div class="bill">
                            <div class="row">
                                <div class="col-lg-6 text-left"><?php echo \TS_Functions::__('Sub total:') ?></div>
                                <div class="col-lg-6 text-right">
                                    <span><?php echo $currency->getSymbol() ?></span>
                                    {{ subTotal | zeroOnLowBalance | numberStyle}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 text-left"><?php echo \TS_Functions::__('Fees:') ?></div>
                                <div class="col-lg-6 text-right">
                                    <span><?php echo $currency->getSymbol() ?></span>
                                    <span v-text="currentFee | zeroOnLowBalance | numberStyle">
                                        <?php echo $currency->formatValue(0) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="row total">
                                <div class="col-lg-6 text-left"><?php echo \TS_Functions::__('Total:') ?></div>
                                <div class="col-lg-6 text-right">
                                    <span><?php echo $currency->getSymbol() ?></span>
                                    <span v-text="amountFinal | zeroOnLowBalance | numberStyle">
                                        <?php echo $currency->formatValue(0) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-green btn-lg" :disabled="disabled || amountError.length > 0">
                        <?php echo \TS_Functions::__('Withdraw') ?>
                    </button>
                </div>
                <div class="status-block<?php echo $session->hasFlash('error_message') ? ' error': ''; ?>">
                    <span>
                        <?php if ($session->hasFlash('error_message')) {
                            echo $session->getFlash('error_message');
                        }
                        if ($session->hasFlash('success_message')) {
                            echo $session->getFlash('success_message');
                        } ?>
                    </span>
                </div>
                <div class="secure-img text-center">
                    <img src="<?php echo \tradersoft\helpers\Assets::findUrl('/img/secure-badge-comodo.png'); ?>" class="img-responsive" alt="secure-badge-comodo">
                    <img src="<?php echo \tradersoft\helpers\Assets::findUrl('/img/secure-badge-rapid.png'); ?>" class="img-responsive" alt="secure-badge-rapid">
                </div>
                <?php \tradersoft\helpers\Form::end()?>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="title"><?php echo \TS_Functions::__('Recent Withdrawals') ?></div>
            <div class="recent-withdrawals">
                <?php if (!empty($requests)) { ?>
                    <table class="recent-withdrawals-table">
                        <thead>
                        <tr>
                            <th class="wd_date"><?php echo \TS_Functions::__('Date') ?></th>
                            <th class="time"><?php echo \TS_Functions::__('Time') ?></th>
                            <th class="amount"><?php echo \TS_Functions::__('Amount') ?></th>
                            <?php if ($recentWithdrawalsViewType == RecentWithdrawalsSettings::AMOUNT_EXCLUDING_FEES) { ?>
                                <th class="fees"><?php echo \TS_Functions::__('Fees') ?></th>
                            <?php }; ?>
                            <th class="status"><?php echo \TS_Functions::__('Status') ?></th>
                            <th class="cancel text-right">
                                <span><?php echo \TS_Functions::__('Cancel') ?></span>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($requests as $wd_request) {
                            $tmp_date = explode(' ', $wd_request['created_at']); ?>
                            <tr>
                                <td class="wd_date"><?php echo $tmp_date[0];?></td>
                                <td class="time"><?php echo $tmp_date[1];?></td>
                                <?php if ($recentWithdrawalsViewType == RecentWithdrawalsSettings::AMOUNT_EXCLUDING_FEES) { ?>
                                    <td class="amount"><?= $currency->renderAmount($wd_request['amountAfterFee']) ?></td>
                                    <td class="fees"><?= $currency->renderAmount($wd_request['feeSum']) ?></td>
                                <?php } elseif ($recentWithdrawalsViewType == RecentWithdrawalsSettings::AMOUNT_INCLUDING_FEES) { ?>
                                    <td class="amount"><?= $currency->renderAmount($wd_request['start_amount']) ?></td>
                                <?php }; ?>
                                <td class="status status-<?php echo $statuses[$wd_request['status']]?>">
                                    <span><?php echo \TS_Functions::__($statuses[$wd_request['status']]) ?></span>
                                </td>
                                <td class="cancel text-right">
                                    <?php if ($wd_request['canCancel']) {
                                        $canCancel = true;
                                    ?>
                                        <i class="close"></i>
                                        <span><?php echo \TS_Functions::__('Cancel') ?></span>

                                        <div id="js-withdrawal-popup" class="withdrawal-popup-wrapper" style="display:none;">
                                            <div class="box-modal-wrapper">
                                                <i class="box-modal-close"></i>
                                                <div class="box-modal-title"><?php echo \TS_Functions::__('Action Required!'); ?></div>
                                                <div class="box-modal-description">
                                                    <p><?php echo \TS_Functions::__('You are about to cancel your pending withdrawal request.'); ?></p>
                                                    <p><?php echo \TS_Functions::__('By proceeding you confirm this action.'); ?></p>
                                                </div>
                                                <a class="button-cancel" data-request="<?php echo $wd_request['id']; ?>" href="javascript:void(0);">Cancel my withdrawal</a>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php if ($canCancel) {
                        $traderWithdrawalCancelModel =
                            TSInit::$app->getVar('traderWithdrawalCancelModel');
                        $cancelForm = \tradersoft\helpers\Form::begin(
                            $traderWithdrawalCancelModel,
                            ['htmlOptions' => ['id' => 'withdrawal_cancel']]
                        );
                        echo \tradersoft\helpers\Html::hiddenInput('tradersoft_submit', 'withdrawal_cancel') .
                            \tradersoft\helpers\Html::hiddenInput('requestId', '');
                        \tradersoft\helpers\Form::end();
                    }
                } else {
                    echo \TS_Functions::__('There are no issued requests yet');
                } ?>
                <span style="display: none;"><?php echo \TS_Functions::__('No withdrawals will appear') ?></span>
            </div>
        </div>
    </div>
</div>

<script>
  (function($){
    $(document).ready(function() {
        $('i.close').click(function(){
            $('#js-withdrawal-popup').css('display', 'flex');
        });

        $('.box-modal-close').click(function(){
            $('#js-withdrawal-popup').css('display', 'none');
        });

        $('.button-cancel').click(function(){
            $("input[name='requestId']").val($(this).data('request'));
            $('#withdrawal_cancel').submit();
        });
    });
  })(window.jQuery);

    var submitWithdrawalForm = true;
    var traderToken = '<?php echo $trader->get('crmId'); ?>';
    var balance = parseFloat('<?php echo $trader->getBalance(); ?>');
    var minAmount = parseFloat('<?php echo $fees['minAmount']; ?>');
    var maintenanceFee = parseFloat('<?php echo $fees['maintenance']; ?>');
    var feeProcessing = <?php echo json_encode($fees['withdrawal']); ?>;
    var roundLvl = <?php echo $currency->getPrecision(); ?>;
    var messages = <?php echo json_encode($messages); ?>;
    var currencySymbol = '<?php echo $currency->getSymbol(); ?>';
</script>

<?php if ($recentWithdrawalsViewType == RecentWithdrawalsSettings::AMOUNT_EXCLUDING_FEES) { ?>
    <style>
        .withdrawal .col-lg-6 {
            float: none;
            margin: 0 auto;
        }
        .withdrawal .col-lg-6 + .col-lg-6 {
            margin-top: 40px;
            width: 100%;
        }
        .recent-withdrawals-table {
            max-width: 100% !important;
        }
    </style>
<?php }; ?>