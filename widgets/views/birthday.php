<?php
/**
 * @var $model \tradersoft\model\account_details\Native
 */
    $current_year = date('Y');
?>
<div class="form-row-select form-row-day">
    <select name="dayNumber" class="olgs_input_select_day">
        <?php for ($i = 1; $i <= 31; $i++): ?>
            <option value="<?php echo $i;?>" <?php echo ($model->dayNumber == $i) ? 'selected' : ''?>><?php echo $i;?></option>
        <?php endfor;?>
    </select>
</div>
<div class="form-row-select form-row-month">
    <select name="monthNumber" class="olgs_input_select_month">
        <?php for ($i = 1; $i <= 12; $i++): ?>
            <option value="<?php echo $i;?>" <?php echo ($model->monthNumber == $i) ? 'selected' : ''?>><?php echo $i;?></option>
        <?php endfor;?>
    </select>
</div>
<div class="form-row-select form-row-year"> 
    <select name="yearNumber" class="olgs_input_select_year">
        <?php for ($i = 1900; $i <= $current_year - 18; $i++): ?>
            <option value="<?php echo $i;?>" <?php echo ($model->yearNumber == $i) ? 'selected' : ''?>><?php echo $i;?></option>
        <?php endfor;?>
    </select>
</div>