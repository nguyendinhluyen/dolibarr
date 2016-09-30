<?php
/* Copyright (C) 2016
 * Config style for printer
 */
?>
<style>
    div.tabsAction_print {
        margin: 20px 0em 10px 0em;
        padding: 0em 0em;
        text-align: right;
        <?php if (GETPOST("optioncss") == 'print') {  ?>
        display: none;
        <?php }?>
    }
    .tabsAction_print {
        float:right;
    }
    tbody {
        font-size: 12px !important;
    }
    #border_print {
        <?php if (GETPOST("optioncss") == 'print') {  ?>
        display: none;
        <?php }?>
    }
    /*Header info*/
    .header_bill {
        <?php if (GETPOST("optioncss") != 'print') {  ?>
        display: none;
        <?php }?>
        float: right;
        clear: both;
    }
    .header_title {
        font-size: 14px;
        height: 20px;
    }
    .info_title {
        height: 20px;
    }
    .customer_bill {
        <?php if (GETPOST("optioncss") != 'print') {  ?>
        display: none;
        <?php }?>
        clear:both;
        padding-top: 20px;
        line-height: 25px;
    }
    
    /*Order info*/
    /*Name*/
    .title_name {
        width: 18%;
    }
    .name {
        width: 32%;
    }
    
    /*Phone*/
    .title_phone {
        width: 15%;
    }
    .phone {
        width: 36%;
    }
    
    /*Order for bill*/
    .order_received_bill {
        <?php if (GETPOST("optioncss") != 'print') {  ?>
        display: none;
        <?php }?>
        clear: both;
        line-height: 25px;
    }
    .check_box_order_received {
        width: 50%;
    }
    .order {
        width: 14%;
    }
    .order_bill_not_done {
        width: 18%;
    }
    .order_bill_done {
        width: 18%;
    }
    
    /*Note*/
    .note_bill {
        <?php if (GETPOST("optioncss") != 'print') {  ?>
        display: none;
        <?php }?>
        line-height: 20px;
        padding-bottom: 20px;
    }
    
    /*Detail product*/
    .print_detail_product, #print_detail_product {
        <?php if (GETPOST("optioncss") == 'print') {  ?>
        display: none;
        <?php }?>
    }
    
    /*Ship*/
    .ship_bill {
        <?php if (GETPOST("optioncss") != 'print') {  ?>
        display: none;
        <?php }?>
        float: right;
    }
    .title_money {
        font-size: 14px;
        font-weight: bold;
        width: 23%;
        padding-left: 50px;
        line-height: 30px;
        padding-top: 5px;
    }
    .number_product {
        width: 12%;
        text-align: right;
        padding-top: 5px;
    }
    .money {
        font-size: 14px;
        font-weight: bold;
        width: 15%;
        text-align: right;
        padding-top: 5px;
    }
    .title_ship {
        font-size: 12px;
        width: 23%;
        padding-left: 50px;
        padding-top: 5px;
    }
    .plus_symbol_ship {
        width: 12%;
        text-align: right;
        padding-top: 5px;
    }
    .fee_ship {
        padding-top: 5px;
        font-size: 12px;
        width: 15%;
        text-align: right;
    }
</style>
<script>
    function eventClickCheckbox() {
        var checkBoxReceivedOrder = document.getElementById("chk_received_order");
        if (checkBoxReceivedOrder.checked === true) {
           document.getElementById("received_name").disabled=true;
           document.getElementById("received_phone").disabled=true;
           document.getElementById("received_address").disabled=true;
           document.getElementById("received_name").value="";
           document.getElementById("received_phone").value="";
           document.getElementById("received_address").value="";
        } else {
           document.getElementById("received_name").disabled=false;
           document.getElementById("received_phone").disabled=false;
           document.getElementById("received_address").disabled=false; 
        }
    }
</script>
    