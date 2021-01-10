<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
    'product_name',
    'product_description',
    'p_category_name',
    db_prefix().'suppliers.name as supplier_name',
    'rate',
    'quantity_number',
    'product_image',
    'taxes',
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'product_master';
$filter       = [];
$where        = [];
$statusIds    = [];
$join         = [
    'LEFT JOIN '.db_prefix().'product_categories ON '.db_prefix().'product_categories.p_category_id='.db_prefix().'product_master.product_category_id','LEFT JOIN '.db_prefix().'suppliers ON '.db_prefix().'suppliers.id='.db_prefix().'product_master.supplier_id',
];
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
     db_prefix().'product_master.id'
]);
$output  = $result['output'];
$rResult = $result['rResult'];


$CI      = &get_instance();
$CI->load->model(['currencies_model']);
$base_currency = $CI->currencies_model->get_base_currency();
foreach ($rResult as $aRow) {
    $row        = [];
    $outputName = '<a href="#">'.$aRow['product_name'].'</a>';
    $outputName .= '<div class="row-options">';
    if (has_permission('products', '', 'delete')) {
        $outputName .= ' <a href="'.admin_url('products/edit/'.$aRow['id']).'" class="_edit">'._l('edit').'</a>';
        $outputName .= '| <a href="'.admin_url('products/delete/'.$aRow['id']).'" class="text-danger _delete">'._l('delete').'</a>';
        $outputName .= '| <a href="'.admin_url('products/dublicate/'.$aRow['id']).'" class="text-success _dublicate">'._l('Duplicate').'</a>';
    }
    $outputName .= '</div>';
    $row[]              = $outputName;
    $row[]              = "<img src='".module_dir_url('products', 'uploads')."/{$aRow['product_image']}' class='img-thumbnail img-responsive zoom' onerror=\"this.src='".module_dir_url('products', 'uploads')."/image-not-available.png'\">";
    $row[]              = $aRow['product_description'];
    $row[]              = $aRow['p_category_name'];

    $row[]              = $aRow['supplier_name'];
    
    $row[]              = app_format_money($aRow['rate'], $base_currency->name);
    $row[]              = $aRow['quantity_number'];
    $row[]              = (!empty($aRow['taxes'])) ? print_taxes($aRow['taxes']) : '';
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}

function print_taxes($taxes): string
{
    $unserialize_taxes = unserialize($taxes);
    if (is_array($unserialize_taxes) && !empty($unserialize_taxes)) {
        return implode(' ', $unserialize_taxes);
    }
}
