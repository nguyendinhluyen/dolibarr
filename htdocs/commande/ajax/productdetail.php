<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/fourn/class/fournisseur.product.class.php';

$idprod = GETPOST('idproduct', 'int');
$entrepot_id = GETPOST('entrepot_id', 'int');
if (!empty($idprod)) {
    $sql = "SELECT dateexpired, reel";
    $sql.= " FROM " . MAIN_DB_PREFIX . "product_stock";
    $sql.= " WHERE fk_product = " . $idprod . " AND reel > 0 AND fk_entrepot = ". $entrepot_id;
    $result = $db->query($sql);
    $date = array();    
    if ($result) {
        $num = $db->num_rows($result);
        if ($num) {
            $i = 0;
            while ($i < $num) {
                $obj = $db->fetch_object($result);
                array_push($date, $obj->dateexpired." Tồn kho: ".$obj->reel);
                $i++;
            }
        }
    }
    echo json_encode($date);
}