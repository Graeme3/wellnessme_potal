<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
    'name',
    'address',
    'phone',
    'contact',
    'email_address',
    'whatsapp_no',
    'description',
];


$sIndexColumn = 'id';
$sTable       = db_prefix().'suppliers';
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);
$output  = $result['output'];
$rResult = $result['rResult'];
$CI      = &get_instance();
$CI->load->model(['currencies_model']);
$base_currency = $CI->currencies_model->get_base_currency();
foreach ($rResult as $aRow) {
    $row   = [];
    $outputName = '<a href="#">'.$aRow['name'].'</a>';
    $outputName .= '<div class="row-options">';
    if (has_permission('products', '', 'delete')) {
        $outputName .= ' <a href="'.admin_url('products/edit_supplier/'.$aRow['id']).'" class="_edit">'._l('edit').'</a>';
        $outputName .= '| <a href="'.admin_url('products/delete_supplier/'.$aRow['id']).'" class="text-danger _delete">'._l('delete').'</a>';
       
    }
    $outputName .= '</div>';
    $row[]              = $outputName;
    $row[]              = $aRow['address'];
    $row[]              = $aRow['phone'];
    $row[]              = $aRow['contact'];
    $row[]              = $aRow['email_address'];
    $row[]              = $aRow['whatsapp_no'];
    $row[]              = $aRow['description'];
   
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
