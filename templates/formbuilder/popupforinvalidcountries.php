<style>
    #ts-popuper-main *, #ts-popuper-main *:before, #ts-popuper-main *:after {
        all: unset;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }
    #ts-popuper-main {
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        position: fixed;
        background: rgba(0, 0, 0, 0.7);
        z-index: 102;
        padding:16px;
        margin: auto;
        display: flex;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    #ts-popuper-main .popuper-block {
        display: flex;
        position:relative;
        background: #ffffff;
        z-index:222;
        max-width: 800px;
        min-width: 200px;
        margin: auto;
        padding: 32px;
        text-align:center;
        pointer-events:all;
    }
    #ts-popuper-main .popuper-block .popup-content {
        text-align:center;
        width: 100%;
    }
    #ts-popuper-main .popuper-block h4 {
        font: 18px Arial,sans-serif;
        margin: 0 0 16px;
        display: block;
        color: #000;

    }
    #ts-popuper-main .popuper-block .btn {
        all:initial;
        display: inline-flex;
        height: 40px;
        background: #000;
        border: 1px solid #000;
        color: #fff;
        font:bold 14px arial,sans-serif;
        padding: 0 24px;
        cursor: pointer;
        margin: 16px 8px 0;
        word-break: break-word;
    }
    #ts-popuper-main .popuper-block .btn:hover {
        background:transparent;
        color:#000;
    }
</style>
<div id="ts-popuper-main" class="popuper-modal overlay center" style="display: none;">
    <div class="popuper-block">
        <div class="popup-content">
            <h4>
                <?php echo \TS_Functions::__('Dear Visitor, Please note that we do not accept clients from this region. For more information please contact our support.'); ?>
            </h4>
            <button class="btn btn-lg btn-green" id="btn-popup-close"><?php echo \TS_Functions::__('Ok'); ?></button>
        </div>
    </div>
</div>
