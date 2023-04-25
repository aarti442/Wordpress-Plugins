<?php
/** 
 * The file used to manage counter style 4.
 * 
 * LICENSE: The contents of this file are subject to the license agreement ("License") which is included in the installation package (LICENSE.txt). By installing or using this file, you have unconditionally agreed to the terms and conditions of the License, and you may not use this file except in compliance with the License. 
 * 
 * @author Biztech Consultancy
 */
?>

<!-- Counter style 4 Start-->
<div id="<?php echo $counter_block_id; ?>" class="<?php echo $module_name; ?>-counter-block number-block number-incident-block counter-block-style-4 bcp-block-div">
    <div class="card-body">
        
        <div class="bcp-row">
            <div class="bcp-col-auto icon-block">
                <em class="<?php echo $icon; ?>" aria-hidden="true"></em>
            </div>
            <div class="bcp-col counter-name">
                <h2><?php _e($module_title, 'bcp_portal'); ?></h2>
                <h3><?php echo $counter; ?></h3>
            </div>
        </div>
    </div>
</div>
<!-- Counter style 4 End-->