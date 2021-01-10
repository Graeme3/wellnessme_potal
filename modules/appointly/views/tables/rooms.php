<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
    'name',
    'service_id',
    'staff_id',
];


$join = [];


$sIndexColumn = 'id';
$sTable       = db_prefix().'rooms';


$details = new ReflectionFunction('data_tables_init'); 
// print $details->getFileName() . ':' . $details->getStartLine();
// exit();

$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], ['id']);

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

    

    $staff_id = explode(",",$aRow['staff_id']);

    $staffname = [];
    if(!empty($staff_id)){

        foreach ($staff_id as $key => $value) {
            $staffname [] = get_staff_from_service($value);
        }

    }
    $staff = implode(",",$staffname);
    

    $service_id = explode(",",$aRow['service_id']);

    $servicename = [];
    if(!empty($service_id)){

        foreach ($service_id as $key => $value) {
            $servicename [] = get_service_from_room($value);
        }

    }
    $service = implode(",",$servicename);
   
    
    $outputName = '<a href="#">'.$aRow['name'].'</a>';
    $outputName .= '<div class="row-options">';
    if (has_permission('rooms', '', 'delete')) {
        $outputName .= ' <a href="'.admin_url('appointly/appointments/edit_room/'.$aRow['id']).'" class="_edit">'._l('edit').'</a>';
        $outputName .= '| <a href="'.admin_url('appointly/appointments/delete_room/'.$aRow['id']).'" class="text-danger _delete">'._l('delete').'</a>';
       
    }
    $outputName .= '</div>';
    $row[]              = $outputName;
    $row[]              = $service;
    $row[]              = $staff;
   
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
