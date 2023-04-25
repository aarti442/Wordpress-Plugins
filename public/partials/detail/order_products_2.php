<?php
/** 
 * The file used to manage order product style 2.
 * 
 * LICENSE: The contents of this file are subject to the license agreement ("License") which is included in the installation package (LICENSE.txt). By installing or using this file, you have unconditionally agreed to the terms and conditions of the License, and you may not use this file except in compliance with the License. 
 * 
 * @author Biztech Consultancy
 */

if (isset($products) && !empty((array) $products)) {
    ?>
    <!-- listing-data-table order product Start -->
    <div class="listing-data-table">
        <table class="bcp-table listing-style-2">
            <thead>
                <tr>
                    <th><?php _e('Item Name'); ?></th>
                    <th><?php _e('Product Code'); ?></th>
                    <th><?php _e('Quantity'); ?></th>
                    <th><?php _e('Unit Price'); ?></th>
                    <th><?php _e('Total Price'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $index => $item) { ?>
                    <tr>
                        <td class="dcp_hover_td"><?php echo isset($item->Name) ? $item->Name : '-'; ?></td>
                        <td class="dcp_hover_td"><?php echo isset($item->ProductCode) ? $item->ProductCode : ''; ?></td>
                        <td class="dcp_hover_td"><?php echo ( isset($item->Quantity) && $item->Quantity != '' ) ? $item->Quantity : '0'; ?></td>
                        <td class="dcp_hover_td"><?php echo ( isset($item->UnitPrice) && $item->UnitPrice != '' ) ? $_SESSION['bcp_default_currency'] . ' ' . number_format($item->UnitPrice, 2) : '0.00' . ' ' . $_SESSION['bcp_default_currency']; ?></td>
                        <td class="dcp_hover_td"><?php echo ( isset($item->TotalPrice) && $item->TotalPrice != '' ) ? $_SESSION['bcp_default_currency'] . ' ' . number_format($item->TotalPrice, 2) : '0.00' . ' ' . $_SESSION['bcp_default_currency']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <!-- listing-data-table order product End -->
<?php } else { ?>
    <strong><?php _e('No Records Found.', 'bcp_portal'); ?></strong>
<?php } ?>
