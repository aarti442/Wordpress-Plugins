<?php
/** 
 * The file used to manage product selection.
 * 
 * LICENSE: The contents of this file are subject to the license agreement ("License") which is included in the installation package (LICENSE.txt). By installing or using this file, you have unconditionally agreed to the terms and conditions of the License, and you may not use this file except in compliance with the License. 
 * 
 * @author Biztech Consultancy
 */
?>

<?php if (isset($available_pricebooks) && is_array($available_pricebooks) && count($available_pricebooks) > 0) { ?>
    <!-- bcp-row Start -->
    <div class="bcp-row add-product-block section-div">
        <div class="bcp-col-md-12 bcp-col-lg-12 section-title">
            <h3 class="section_name"><?php echo __("Product Selection"); ?></h3>
            
            <?php if($productlistmenu == true){ ?>
            <div class="listing-block module-createby-product"style="display:none">
                <a href="javascript:void(0)" class="" id="open_id01_product_module_selection"  title="Create">
                    <button class="bcp-btn">
                        <span class="lnr lnr-plus-circle"></span>
                        <p><?php echo __("Create"); ?></p>
                    </button>
                </a>
            </div>
        <?php } ?>
        </div>
        <!-- bcp-col-lg-4 Start -->
       
        <div class="bcp-col-lg-4">
            <!-- bcp-form-group Start -->
            <div class="bcp-form-group">
                <div class="bcp-flex bcp-align-items-center">
                    <label for="pricebook"><?php echo __("Price List"); ?></label>
                    <span class="required">*</span>
                </div>
                <?php
                $all_disabled = 'disabled';
                $all_checked = '';
                if (  $record_id || ( isset($order_products) && is_array($order_products) && count($order_products) > 0 ) ) {
                    $selected_priceleval_id = $order_products[0]->PriceBook2Id;
                    ?>
                    <select <?php echo (!empty($selected_priceleval_id) ? "disabled='disabled'" : ""); ?> class="custom-select pricebook_selection" id="pricebook" name="pricebook" >
                        <option value="<?php echo $selected_priceleval_id; ?>" selected="selected" disabled="disabled" ><?php _e($available_pricebooks[$selected_priceleval_id], 'bcp_portal'); ?></option>
                    </select>
                    <input type="hidden" name="pricebook" value="<?php echo $selected_priceleval_id; ?>"/>
                    <?php
                } else {
                    if (isset($order_products) && is_array($order_products) && count($order_products) > 0) {
                        $selected_priceleval_id = $order_products[0]->PriceBook2Id;
                    }
                    ?>
                    <select required="" class="custom-select pricebook_selection" id="pricebook" name="pricebook" >
                        <option value="" selected="" disabled="" ><?php _e('--None--', 'bcp_portal'); ?></option>
                        <?php
                        foreach ($available_pricebooks as $pricebook_id => $pricebook_name) {
                            $selected_text = '';
                            if (isset($selected_priceleval_id) && $selected_priceleval_id == $pricebook_id) {
                                $selected_text = ' selected=selected ';
                            }
                            ?>
                            <option <?php echo $selected_text; ?> value="<?php echo $pricebook_id; ?>"><?php echo $pricebook_name; ?></option>
                        <?php } ?>
                    </select>
                <?php } ?>
            </div> <!-- bcp-form-group End -->
        </div>
        <!-- bcp-col-lg-4 End -->
        <!-- bcp-col-lg-12 Start -->
        <div class="bcp-col-lg-12">
            <label><?php _e("Select Product Line Items", 'bcp_portal'); ?></label>
            <span class="required" aria-required="true">*</span>
            <!-- add-product-table-block Start -->
            <div class="add-product-table-block">
                <span id="err-msg" class="err-msg error mr-auto" style="display: none;"></span>
                <!-- table-responsive Start -->
                <div class="table-responsive custom-scrollbar">
                    <table class="table quote-product-list-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select_all_product" class="select_all_product" <?php
                                    echo $all_disabled;
                                    echo $all_checked;
                                    ?> title="<?php _e('Select All', 'bcp_portal'); ?>" ></th>
                                <th><?php _e('Product', 'bcp_portal'); ?></th>
                                <th><?php _e('Unit Price', 'bcp_portal'); ?></th>
                                <th><?php _e('Qty', 'bcp_portal'); ?></th>
                                <th><?php _e('Total', 'bcp_portal'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="bcp_get_priceleval_product_list" class="dcp_product_line_items_table">
                            <?php if (!isset($order_products) || $order_products == "") {  ?>
                                <tr>
                                    <td colspan="5"><?php _e('*Please select Pricelist to see list of products.', 'bcp_portal'); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div id="bcp_product_load_more"></div>
                </div>
                <!-- table-responsive End -->
            </div>
            <!-- add-product-table-block End -->
        </div>
        <!-- bcp-col-lg-12 Start -->
    </div>
    <!-- bcp-row End -->
    <?php if (isset($order_products) && is_array($order_products) && count($order_products) > 0) {  ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var contain_products = <?php echo json_encode($order_products, JSON_PRETTY_PRINT) ?>;
                var pricebook = '<?php echo $selected_priceleval_id; ?>';
                var module_name = jQuery("input[name=module_name]").val();
                
                bcp_get_priceleval_product_list(module_name, pricebook, 1, false, contain_products);
                
            });
           
        </script>
        <?php
    }
}
?>
 <script type="text/javascript">
            jQuery(document).ready(function () {
               // var contain_products = <?php echo json_encode($order_products, JSON_PRETTY_PRINT) ?>;
               /// var pricebook = '<?php echo $selected_priceleval_id; ?>';
              //  var module_name = jQuery("input[name=module_name]").val();
              //  bcp_get_priceleval_product_list(module_name, pricebook, 1, false, contain_products);
                var cookie_pricebook = getCookie('pselected_pricebook');
                var cookie_products = getCookie('pselected_products');
                console.log(cookie_pricebook);
                console.log(cookie_products);
                if(cookie_pricebook != "" && cookie_products != ""){
                   jQuery('#pricebook').val(cookie_pricebook);
                   jQuery('select.pricebook_selection').trigger('change');
                   setTimeout(showproducts ,2000);
                 
                    function showproducts(){
                        cookie_products = JSON.parse(cookie_products);
                        console.log(cookie_products)
                    
                        for(i=0;i<cookie_products.length;i++){
                            
                                console.log(cookie_products[i][0] +'-'+cookie_products[i][1])
                                $(".select-product-chk[value="+cookie_products[i][0]+"]").trigger('click');
                                jQuery("input[value="+cookie_products[i][0]+"]").parent().parent().find('.bcp_product_select_qty .product_qty').prop('disabled', false);
                                jQuery("input[value="+cookie_products[i][0]+"]").parent().parent().find('.bcp_product_select_qty .product_qty').val(cookie_products[i][1]);
                                jQuery("input[value="+cookie_products[i][0]+"]").parent().parent().find('.bcp_product_select_qty .product_qty').trigger('change');
                            
                        }
                    }
                }
              
            });
            
            function getCookie(cname) {
                let name = cname + "=";
                let decodedCookie = decodeURIComponent(document.cookie);
                let ca = decodedCookie.split(';');
                for(let i = 0; i <ca.length; i++) {
                    let c = ca[i];
                    while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                    }
                }
                return "";
                }
</script>
<?php if($productlistmenu == true){ ?> 
<div id="id01_product_module_selection" class="w3-modal common-popup-wrapper" style="display:none;">
    <div class="w3-modal-content w3-animate-top w3-card-4">
        <div class="case-listing-block case-listing-block-1 sfcp-loan-accept-block bg-white">
            <header class="w3-container w3-teal"> 
                <span onclick="document.getElementById('id01_product_module_selection').style.display='none'" 
                class="w3-button w3-display-topright">&times;</span>
            </header>
            <div class="bcp-container-fluid">
            <div class="bcp-row">
               
                <div class="add-case-block add-product-block">
                    <div class="heading-part">
                        <p class="module_title" id="dcp-head-incident">
                            <?php _e('Select object from below', 'bcp_portal'); ?>
                        </p>
  
                    </div>

                    
                    <div class="module_for_products">
                        <form action="#" name="module_for_selected_products" id="module_for_selected_products" method="post">
                            <ul>
                                <?php 
                                
                                foreach($_SESSION['bcp_module_access_data'] as $key => $value){ 
                                        $label = $_SESSION['bcp_modules'][$key]['singular'];
                                        $productselection = $value->productSelection;
                                        if($productselection == "true"){ ?>
                                        <li><input type="radio" class="module_radio" name="module_radio" required value="<?php echo $key;?>"><label><?php echo $label;?> </label></li>
                                        <?php } ?>
                                <?php } ?>
                            </ul>
                            <input type="hidden" name="selected_pricebook" class="selected_pricebook">
                            <input type="hidden" name="selected_products" class="selected_products">
                            <div class="case-button-group">
                            <button type="submit" class="bcp-btn save-btn">Save</button>
                            </div>
                        </form>
                    </div>                  
                </div>                  
            </div>
           </div>
        </div>
    </div>
</div> 
<?php } ?>