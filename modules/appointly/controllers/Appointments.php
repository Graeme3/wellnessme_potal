<?php defined('BASEPATH') or exit('No direct script access allowed');

class Appointments extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('appointly_model', 'apm');
    }

    /**
     * Main view
     *
     * @return void
     */
    public function index()
    {
        if (!staff_can('view', 'appointments') && !staff_can('view_own', 'appointments')) {
            access_denied('Appointments');
        }

        $data['td_appointments'] = $this->getTodaysAppointments();

        $this->load->view('index', $data);
    }

    // public function uploadstaff(){

        
    //     $this->apm->importServices();

    // }

    /**
     * Single appointment view
     *
     * @return void
     */
    public function view()
    {
        $appointment_id = $this->input->get('appointment_id');

        $attendees = $this->atm->attendees($appointment_id);


        $appointment_service_detail = $this->apm->get_appointment_services_detail1_data($appointment_id);

        // print_r($appointment_service_detail);
        // exit();

        $data['appointment_service_detail'] = $appointment_service_detail;

       
        /**
         * If user is assigned to a appointment but have no permissions at all eg. edit or view
         * User will be able to open the url send to mail (But only to view this specific meeting or meetings that the user is assigned to)
         */
        if (!in_array(get_staff_user_id(), $attendees)) {
            if (!staff_can('view', 'appointments') && !staff_can('view_own', 'appointments')) {
                access_denied('Appointments');
            }
        }

        $data['appointment'] = fetch_appointment_data($appointment_id);

        if ($data['appointment']) {
            $data['appointment']['public_url'] = site_url('appointly/appointments_public/client_hash?hash=' . $data['appointment']['hash']);
        } else {
            appointly_redirect_after_event('warning', _l('appointment_not_exists'));
        }

        if (!$data['appointment']) {
            show_404();
        }

        $this->load->view('tables/appointment', $data);
    }

    /**
     * Render table view
     *
     * @return void
     */
    public function table()
    {
        if (!staff_can('view', 'appointments') && !staff_can('view_own', 'appointments')) {
            access_denied();
        }

        $this->app->get_table_data(module_views_path(APPOINTLY_MODULE_NAME, 'tables/index'));
    }

    public function fetch_contact_data()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $id = $this->input->post('contact_id');

        header('Content-Type: application/json');
        echo json_encode($this->apm->apply_contact_data($id));
    }

    /**
     * Modal edit and modal update trigger views with data
     *
     * @return void
     */
    public function modal()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->load->model('staff_model');

        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);


        $data['services'] =  $this->apm->get_services_data();

        $data['rooms'] =  $this->apm->get_rooms_data();

        

        $data['contacts'] = appointly_get_staff_customers();

        if ($this->input->post('slug') === 'create') {

            $this->load->view('modals/create', $data);
        } else if ($this->input->post('slug') === 'update') {

            $data['appointment_id'] = $this->input->post('appointment_id');

            $data['history'] = fetch_appointment_data($data['appointment_id']);

            $data['history'] = fetch_appointment_data($data['appointment_id']);

            $data['appointment_services'] =  $this->apm->get_appointment_services_data($data['appointment_id']);
            

            if (isset($data['notes'])) {
                $data['notes'] = htmlentities($data['notes']);
            }

            $this->load->view('modals/update', $data);
        }
    }

    /**
     * Update appointment
     *
     * @return void
     */
    public function update()
    {
        if (!staff_can('edit', 'appointments')) {
            access_denied();
        }
        $appointment = $this->input->post();
        $appointment['notes'] = $this->input->post('notes', false);

        if ($appointment) {
            if ($this->apm->update_appointment($appointment)) {
                appointly_redirect_after_event('success', _l('appointment_updated'));
            }
        }
    }

    /**
     * Create appointment
     *
     * @return void
     */
    public function create()
    {
        if (!staff_can('create', 'appointments')) {
            access_denied();
        }
        $data = array();

        $data = $this->input->post();

        // print_r($data);
        // exit;
        if (!empty($data)) {
            if ($this->apm->insert_appointment($data)) {
                appointly_redirect_after_event('success', _l('appointment_created'));
            }
        }
    }

    /**
     * Delete appointment
     *
     * @param [type] appointment $id
     * @return void
     */
    public function delete($id)
    {
        if (!staff_can('delete', 'appointments')) {
            access_denied();
        }

        if (isset($id)) {
            if ($this->apm->delete_appointment($id)) {
                appointly_redirect_after_event('success', _l('appointment_deleted'));
            }
        } else {
            show_404();
        }
    }


    /**
     * Approve new appointment
     *
     * @return void
     */
    public function approve()
    {
        if (!is_admin()) {
            access_denied();
        }

        if ($this->apm->approve_appointment($this->input->get('appointment_id'))) {
            appointly_redirect_after_event('success', _l('appointment_appointment_approved'));
        }
    }

    /**
     * Mark appointment as finished
     *
     * @return json
     */
    public function finished()
    {
        if (!staff_can('view', 'appointments') && !staff_can('view_own', 'appointments')) {
            access_denied();
        }

        $id = $this->input->post('id');

        $appointment = $this->apm->get_appointment_data($id);

        if (!is_admin() && $appointment['created_by'] !== get_staff_user_id()) {
            access_denied();
        }

        return $this->apm->mark_as_finished($id);
    }

    /**
     * Mark appointment as ongoing
     *
     * @return json
     */
    public function mark_as_ongoing_appointment()
    {
        if (!staff_can('view', 'appointments') && !staff_can('view_own', 'appointments')) {
            access_denied();
        }

        $id = $this->input->post('id');

        $appointment = $this->apm->get_appointment_data($id);
        if (!is_admin() && $appointment['created_by'] != get_staff_user_id()) {
            access_denied();
        }

        return $this->apm->mark_as_ongoing($appointment);
    }

    /**
     * Mark appointment as cancelled
     *
     * @return json
     */
    public function cancel_appointment()
    {
        if (!staff_can('view', 'appointments') && !staff_can('view_own', 'appointments')) {
            access_denied();
        }

        $id = $this->input->post('id');

        $appointment = $this->apm->get_appointment_data($id);
        if (!is_admin() && $appointment['created_by'] != get_staff_user_id()) {
            access_denied();
        }

        return $this->apm->cancel_appointment($id);
    }

    /**
     * Get todays appointments
     *
     * @return array
     */
    public function getTodaysAppointments()
    {
        return $this->apm->fetch_todays_appointments();
    }

    /**
     * Send appointment early reminders
     *
     * @param [string] appointment_id
     * @return json
     */
    public function send_appointment_early_reminders()
    {
        if (!staff_can('view', 'appointments') && !staff_can('view_own', 'appointments')) {
            access_denied();
        }
        if ($this->apm->send_appointment_early_reminders($this->input->post('id'))) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }


    /** 
     * Load user settings view
     * @return view
     */
    public function user_settings_view()
    {
        $data = [];

        if (!staff_can('view', 'appointments') && !staff_can('view_own', 'appointments')) {
            access_denied();
        }

        $data = getAppoinlyUserMeta();

        $data['filters'] = get_appointments_table_filters();

        $this->load->view('users/index', $data);
    }



    /** 
     * User settings request for updating options in meta table
     * @return void
     */
    public function user_settings()
    {
        $data  = $this->input->post();

        if ($data) {

            $meta = [
                'appointly_show_summary' => $this->input->post('appointly_show_summary'),
                'appointly_default_table_filter' => $this->input->post('appointly_default_table_filter'),
            ];

            $this->apm->update_appointment_types(
                $data,
                $meta
            );

            appointly_redirect_after_event('success', _l('settings_updated'), 'appointments/user_settings_view/settings');
        }
    }

    /** 
     * Add new appointment type
     * @return json
     */
    public function new_appointment_type()
    {
        if (!staff_can('create', 'appointments')) {
            access_denied();
        }
        if ($this->input->post()) {
            if ($this->apm->new_appointment_type(
                $this->input->post('type'),
                $this->input->post('color')
            )) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            }
        }
        return false;
    }


    /**
     * Delete appointment type
     *@param [string] id
     * @return boolean
     */
    public function delete_appointment_type()
    {
        if (!staff_can('delete', 'appointments')) {
            access_denied();
        }
        return $this->apm->delete_appointment_type($this->input->post('id'));
    }

    /** 
     * Add event to google calendar
     * @return json
     */
    public function addEventToGoogleCalendar()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (!staff_can('edit', 'appointments')) {
            access_denied();
        }

        $data = array();

        $data = $this->input->post();
        if (!empty($data)) {
            header('Content-Type: application/json');
            $result = $this->apm->add_event_to_google_calendar($data);
            if ($result) {
                echo json_encode($result);
            } else {
                echo json_encode($result);
            }
        }
    }

    public function services()
    {

        if (!is_admin()) {
            access_denied('services');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path(APPOINTLY_MODULE_NAME, 'tables/services'));
        }

        
        $data['title']         = _l('services');
        $this->load->view('services', $data);
    }

    public function add_service()
    {
       
        if (!has_permission('services', '', 'view')) {
            access_denied('services View');
        }

        close_setup_menu();


        if (has_permission('services', '', 'view')) {
            $post          = $this->input->post();


            $this->load->model('staff_model');

            $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);
            if (!empty($post)) {
                //     print_r($post);
                // exit();
                $this->form_validation->set_rules('title', 'Title', 'required');
                $this->form_validation->set_rules('category', 'Category', 'required');
                // $this->form_validation->set_rules('provider', 'Provider', 'required');
                $this->form_validation->set_rules('color', 'Color', 'required');
                 $this->form_validation->set_rules('service_duration', 'Service duration', 'required');
                $this->form_validation->set_rules('visibility', 'Visibility', 'required');
                $this->form_validation->set_rules('price', 'Price', 'required');
              
                if (false == $this->form_validation->run()) {
                    set_alert('danger', preg_replace("/\r|\n/", '', validation_errors()));
                } else {

                    $provider ='';
                    if(!empty($post['provider'])){
                         $provider = implode(",", $post['provider']);
                    }

                   

                    $data = [
                        'title'        => $post['title'],
                        'category' => $post['category'] ?? '',
                        'provider' => $provider ?? '',
                        'color'                => $post['color'] ?? '',
                        'service_duration'                => $post['service_duration'] ?? '',
                        'visibility'     => $post['visibility'] ?? '',
                        'price'               => $post['price'] ?? '',
                    ];
                   
                    $inserted_id    = $this->apm->add_service($data);

                     $extra_data = [
                        'service_id'        => $inserted_id,
                        'title'        => $post['extra_title'],
                        'service_duration'                => $post['extra_duration'],
                        'quantity'     => $post['extra_quantity'],
                        'price'               => $post['extra_price'],
                    ];

                    $this->apm->add_extra_service($extra_data);


                    if ($inserted_id) {
                        set_alert('success', 'Service Added successfully');
                        redirect(admin_url('appointly/appointments/services'), 'refresh');
                    } else {
                        set_alert('warning', _l('Error Found - Service not inserted'));
                    }
                }
            }
            // $this->load->model(['currencies_model', 'product_category_model']);
            $data['title']              = _l('Add service');
            $data['action']             = _l('service');

            $this->load->view('add_service', $data);
        } else {
            access_denied('services');
        }
    }

    public function edit_service($id)
    {
        if (!has_permission('services', '', 'view')) {
            access_denied('services View');
        }

        close_setup_menu();

        if (has_permission('services', '', 'view')) {
            $original_product = $data['service'] = $this->apm->get_by_id_service($id);

            $data['extra_services'] = $this->apm->get_by_id_extra_service($id);

            if (empty($original_product)) {
                set_alert('danger', _l('not_found_products'));
                redirect(admin_url('appointly/appointments/services'), 'refresh');
            }
            $post=$this->input->post();
            $this->load->model('staff_model');

            $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);

            if (!empty($post)) {


                    
                $this->form_validation->set_rules('title', 'Title', 'required');
                $this->form_validation->set_rules('category', 'Category', 'required');
                // $this->form_validation->set_rules('provider', 'Provider', 'required');
                $this->form_validation->set_rules('color', 'Color', 'required');
                 $this->form_validation->set_rules('service_duration', 'Service duration', 'required');
                $this->form_validation->set_rules('visibility', 'Visibility', 'required');
                $this->form_validation->set_rules('price', 'Price', 'required');
              
                if (false == $this->form_validation->run()) {
                    set_alert('danger', preg_replace("/\r|\n/", '', validation_errors()));
                } else {

              
                    $provider ='';
                    if(!empty($post['provider'])){
                         $provider = implode(",", $post['provider']);
                    }

                    $data = [
                        'title'        => $post['title'],
                        'category' => $post['category'] ?? '',
                        'provider' => $provider ?? '',
                        'color'                => $post['color'] ?? '',
                        'service_duration'                => $post['service_duration'] ?? '',
                        'visibility'     => $post['visibility'] ?? '',
                        'price'               => $post['price'] ?? '',
                    ];
                    

                    $result=$this->apm->edit_service($data, $id);

                     $extra_data = [
                        'service_id'        => $id,
                        'title'        => $post['extra_title'],
                        'service_duration'                => $post['extra_duration'],
                        'quantity'     => $post['extra_quantity'],
                        'price'               => $post['extra_price'],
                    ];

                    $this->apm->edit_extra_service($extra_data,$id);
                   
                    if ($result) {
                        set_alert('success', 'Service Updated successfully');
                        redirect(admin_url('appointly/appointments/services'), 'refresh');
                    } else {
                        set_alert('warning', _l('Error Found Or You Have not made any changes'));
                    }
                }
            }
            $data['title']              = _l('edit', 'Service');
            $data['services'] = $this->apm->get_by_id_service();



           
            $this->load->view('add_service', $data);
        } else {
            access_denied('products');
        }
    }


    public function delete_service($id)
    {
        if (!is_admin()) {
            access_denied('Delete Service');
        }
        if (!$id) {
            redirect(admin_url('appointly/appointments/services'));
        }
        $response = $this->apm->delete_by_id_service($id);
        if (true == $response) {
            set_alert('success', _l('deleted', _l('services')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('services')));
        }
        redirect(admin_url('appointly/appointments/services'));
    }

    public function rooms()
    {

        if (!is_admin()) {
            access_denied('services');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path(APPOINTLY_MODULE_NAME, 'tables/rooms'));
        }

        
        $data['title']         = _l('rooms');
        $this->load->view('rooms', $data);
    }

    public function add_room()
    {
       
        if (!has_permission('rooms', '', 'view')) {
            access_denied('rooms View');
        }

        close_setup_menu();


        if (has_permission('rooms', '', 'view')) {
            $post          = $this->input->post();



            $this->load->model('staff_model');

            $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);
            $data['services'] =  $this->apm->get_services_data();

            if (!empty($post)) {


                $this->form_validation->set_rules('name', 'Name', 'required');
                $this->form_validation->set_rules('[service_id]', 'Service ID', 'required');
                $this->form_validation->set_rules('[staff_id]', 'Staff ID', 'required');
              
                if (false == $this->form_validation->run()) {
                    set_alert('danger', preg_replace("/\r|\n/", '', validation_errors()));
                } else {

                    
                    $service_id ='';
                    if(!empty($post['service_id'])){
                         $service_id = implode(",", $post['service_id']);
                    }

                    $staff_id ='';
                    if(!empty($post['staff_id'])){
                         $staff_id = implode(",", $post['staff_id']);
                    }

                    $data = [
                        'name'        => $post['name'],
                        'service_id' => $service_id ?? '',
                        'staff_id' => $staff_id ?? '',
                    ];
                   
                    $inserted_id    = $this->apm->add_room($data);

                    if ($inserted_id) {
                        set_alert('success', 'Room Added successfully');
                        redirect(admin_url('appointly/appointments/rooms'), 'refresh');
                    } else {
                        set_alert('warning', _l('Error Found - Rooms not inserted'));
                    }
                }
            }

            $data['title']              = _l('Add Room');
            $data['action']             = _l('room');

            $this->load->view('add_room', $data);
        } else {
            access_denied('rooms');
        }
    }

    public function edit_room($id)
    {
        if (!has_permission('rooms', '', 'view')) {
            access_denied('rooms View');
        }

        close_setup_menu();

        if (has_permission('rooms', '', 'view')) {
            $original_product = $data['room'] = $this->apm->get_by_id_room($id);

           

            if (empty($original_product)) {
                set_alert('danger', _l('not_found_room'));
                redirect(admin_url('appointly/appointments/rooms'), 'refresh');
            }
            $post=$this->input->post();
            $this->load->model('staff_model');

            $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);
            $data['services'] =  $this->apm->get_services_data();

            if (!empty($post)) {


                    
                $this->form_validation->set_rules('name', 'Name', 'required');
                $this->form_validation->set_rules('[service_id]', 'Service ID', 'required');
                $this->form_validation->set_rules('[staff_id]', 'Staff ID', 'required');
              
                if (false == $this->form_validation->run()) {
                    set_alert('danger', preg_replace("/\r|\n/", '', validation_errors()));
                } else {

              
                   $service_id ='';
                    if(!empty($post['service_id'])){
                         $service_id = implode(",", $post['service_id']);
                    }

                    $staff_id ='';
                    if(!empty($post['staff_id'])){
                         $staff_id = implode(",", $post['staff_id']);
                    }

                    $data = [
                        'name'        => $post['name'],
                        'service_id' => $service_id ?? '',
                        'staff_id' => $staff_id ?? '',
                    ];
                    

                    $result=$this->apm->edit_room($data, $id);
                   
                    if ($result) {
                        set_alert('success', 'Room Updated successfully');
                        redirect(admin_url('appointly/appointments/rooms'), 'refresh');
                    } else {
                        set_alert('warning', _l('Error Found Or You Have not made any changes'));
                    }
                }
            }
            $data['title']         = _l('edit', 'Room');
            $data['rooms'] = $this->apm->get_by_id_room();



           
            $this->load->view('add_room', $data);
        } else {
            access_denied('rooms');
        }
    }


    public function delete_room($id)
    {
        if (!is_admin()) {
            access_denied('Delete Room');
        }
        if (!$id) {
            redirect(admin_url('appointly/appointments/rooms'));
        }
        $response = $this->apm->delete_by_id_room($id);
        if (true == $response) {
            set_alert('success', _l('deleted', _l('rooms')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('rooms')));
        }
        redirect(admin_url('appointly/appointments/rooms'));
    }


    public function calendar()
    {
        $rooms =  $this->apm->get_rooms_data();
        $room = [];
         foreach ($rooms as $key => $value) {
            $room[$key]['id'] = $value['id'];
            $room[$key]['title'] = $value['name'];
        }

        $data['room'] = json_encode($room);


        $appointments =  $this->apm->fetch_all_appointments();
        $appointment = [];
         foreach ($appointments as $key => $value) {
            $appointment_service_detail = $this->apm->get_appointment_services_detail1_data($value['id']);
            $appointment_service_detail =  implode('- ', array_map(function ($entry) {
              return $entry['title'];
            }, $appointment_service_detail));

            $appointment[$key]['id'] = $value['id'];
            $appointment[$key]['resourceId'] = $value['room_id'];
            $appointment[$key]['start'] = $value['date'].'T'.$value['start_hour'].':00';
            $appointment[$key]['title'] = $value['name'].', services : '.$appointment_service_detail;

        }

        $data['appointment'] = json_encode($appointment);

        // print_r($data['appointment']);
        // exit();



        if (!is_admin()) {
            access_denied('calender');
        }

        
        $data['title']         = _l('calender');
        $this->load->view('calender', $data);
    }


}
