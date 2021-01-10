<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
    'title',
    'category',
    db_prefix().'service.color as service_color',
    'service_duration',
    'visibility',
    'price',
    'provider',
    db_prefix().'appointly_appointment_types.type as service_type',
];


$join = [
    'LEFT JOIN '.db_prefix().'appointly_appointment_types ON '.db_prefix().'appointly_appointment_types.id = '.db_prefix().'service.category',
];


$sIndexColumn = 'id';
$sTable       = db_prefix().'service';


$details = new ReflectionFunction('data_tables_init'); 
// print $details->getFileName() . ':' . $details->getStartLine();
// exit();

$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], [db_prefix().'service.id']);

// echo "hello";
// exit();
$output  = $result['output'];


// print_r($output);
// exit();

$rResult = $result['rResult'];
$CI      = &get_instance();
// $CI->load->model(['currencies_model']);
// $base_currency = $CI->currencies_model->get_base_currency();


foreach ($rResult as $aRow) {
    $row   = [];

    

    $provider = explode(",",$aRow['provider']);

    // print_r($provider);
    // exit();

    $staffname = [];
    if(!empty($provider)){

        foreach ($provider as $key => $value) {
            $staffname [] = get_staff_from_service($value);
        }

    }


    $staff = implode(",",$staffname);
    
    $outputName = '<a href="#">'.$aRow['title'].'</a>';
    $outputName .= '<div class="row-options">';
    if (has_permission('products', '', 'delete')) {
        $outputName .= ' <a href="'.admin_url('appointly/appointments/edit_service/'.$aRow['id']).'" class="_edit">'._l('edit').'</a>';
        $outputName .= '| <a href="'.admin_url('appointly/appointments/delete_service/'.$aRow['id']).'" class="text-danger _delete">'._l('delete').'</a>';
       
    }
    $outputName .= '</div>';
    $row[]              = $outputName;
    // $row[]              = $aRow['title'];
    $row[]              = $aRow['service_type'];
    $row[]              = $aRow['service_color'];
    $row[]              = $aRow['service_duration']?$aRow['service_duration'].' min':'';
    $row[]              = $aRow['visibility'];
    $row[]              = $aRow['price'];
    $row[]              = $staff;
   
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
