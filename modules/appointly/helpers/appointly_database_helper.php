<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('init_appointly_database_tables')) {
    /**
     * Init installation tables creation in database
     */
    function init_appointly_database_tables()
    {
        $CI = &get_instance();

        add_option('appointly_responsible_person', '');
        add_option('callbacks_responsible_person', '');
        add_option('appointly_show_clients_schedule_button', 0);
        add_option('appointly_tab_on_clients_page', 0);
        add_option('appointly_also_delete_in_google_calendar', 1);
        add_option('appointments_show_past_times', 1);
        add_option('appointments_disable_weekends', 1);
        add_option('appointly_client_meeting_approved_default', 0);
        add_option('appointly_google_client_secret', '');

        add_option(
            'appointly_available_hours',
            '["08:00","08:30","09:00","09:30","10:00","10:30","11:00","11:30","12:00","12:30","13:00","13:30","14:00","14:30","15:00","15:30","16:00","16:30","17:00"]'
        );

        add_option('appointly_busy_times_enabled', '1');
        add_option('callbacks_mode_enabled', '1');
        add_option('appointly_appointments_recaptcha', '0');

        $CI->db->query(
            "CREATE TABLE IF NOT EXISTS " . db_prefix() . "appointly_appointments (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `google_event_id` varchar(191) DEFAULT NULL,
                `google_calendar_link` varchar(191) DEFAULT NULL,
                `google_added_by_id` int(11) DEFAULT NULL,
                `subject` varchar(191) NOT NULL,
                `description` text,
                `email` varchar(191) DEFAULT NULL,
                `name` varchar(191) DEFAULT NULL,
                `phone` varchar(191) DEFAULT NULL,
                `address` varchar(191) DEFAULT NULL,
                `notes` longtext DEFAULT NULL,
                `contact_id` int(11) DEFAULT NULL,
                `by_sms` tinyint(1) DEFAULT NULL,
                `by_email` tinyint(1) DEFAULT NULL,
                `hash` varchar(191) DEFAULT NULL,
                `notification_date` datetime DEFAULT NULL,
                `external_notification_date` datetime DEFAULT NULL,
                `date` date NOT NULL,
                `start_hour` varchar(191) NOT NULL,
                `approved` tinyint(1) NOT NULL DEFAULT '0',
                `created_by` int(11) DEFAULT NULL,
                `reminder_before` int(11) DEFAULT NULL,
                `reminder_before_type` varchar(10) DEFAULT NULL,
                `finished` tinyint(1) NOT NULL DEFAULT '0',
                `cancelled` tinyint(1) NOT NULL DEFAULT '0',
                `cancel_notes` text,
                `source` varchar(191) DEFAULT NULL,
                `type_id` int(11) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;"
        );

        $CI->db->query(
            "CREATE TABLE IF NOT EXISTS " . db_prefix() . "appointly_attendees (
                `staff_id` int(11) NOT NULL,
                `appointment_id` int(11) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        );
        $CI->db->query(
            "CREATE TABLE IF NOT EXISTS " . db_prefix() . "appointly_appointment_types (
               `id` int(11) NOT NULL AUTO_INCREMENT,
               `type` varchar(191) NOT NULL,
               `color` varchar(191) NOT NULL,
               PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        );
        $CI->db->query(
            "CREATE TABLE IF NOT EXISTS " . db_prefix() . "appointly_google (
               `id` int(11) NOT NULL AUTO_INCREMENT,
               `staff_id` int(11) NOT NULL,
               `access_token` varchar(191) NOT NULL,
               `refresh_token` varchar(191) NOT NULL,
               `expires_in` varchar(191) NOT NULL,
               PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        );

        $CI->db->query(
            "CREATE TABLE IF NOT EXISTS " . db_prefix() . "appointly_callbacks (
               `id` int(11) NOT NULL AUTO_INCREMENT,
               `call_type` varchar(191) NOT NULL,
               `phone_number` varchar(191) NOT NULL,
               `timezone` varchar(191) NOT NULL,
               `firstname` varchar(191) NOT NULL,
               `lastname` varchar(191) NOT NULL,
               `status` varchar(191) NOT NULL DEFAULT '1',
               `message` text NOT NULL,
               `email`  varchar(191) NOT NULL ,
               `date_start` datetime NOT NULL,
               `date_end` datetime NOT NULL,
               `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
               PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        );

        $CI->db->query(
            "CREATE TABLE IF NOT EXISTS " . db_prefix() . "appointly_callbacks_assignees (
               `id` int(11) NOT NULL AUTO_INCREMENT,
               `callbackid` int(11) NOT NULL,
               `user_id` int(11) NOT NULL,
               PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        );

        checkForModuleReinstallation();
    }
}


if (!function_exists('init_appointly_template_tables')) {
    /**
     * Insert email templates into database
     */
    function init_appointly_template_tables()
    {
        create_email_template('You have an upcoming appointment !', '<span style=\"font-size: 12pt;\"> Hello {staff_firstname} {staff_lastname} </span><br /><br /><span style=\"font-size: 12pt;\"> You have an upcoming appointment that is need to be held date {appointment_date} and location {appointment_location}</span><br /><br /><span style=\"font-size: 12pt;\"><strong>Additional info for your appointment:</strong></span><br /><span style=\"font-size: 12pt;\"><strong>Appointment Subject:</strong> {appointment_subject}</span><br /><span style=\"font-size: 12pt;\"><strong>Appointment Description:</strong> {appointment_description}</span><br /><span style=\"font-size: 12pt;\"><strong>Appointment scheduled date to start:</strong> {appointment_date}</span><br /><span style=\"font-size: 12pt;\"><strong>You can view this appointment at the following link:</strong> <a href="{appointment_admin_url}">Your appointment URL</a></span><br /><span style=\"font-size: 12pt;\"><br />Kind Regards</span><br /><br /><span style=\"font-size: 12pt;\">{email_signature}</span>', 'appointly', 'Appointment reminder (Sent to Staff and Attendees)', 'appointment-cron-reminder-to-staff');

        create_email_template('Appointment has been cancelled !', '<span style=\"font-size: 12pt;\"> Hello {staff_firstname} {staff_lastname}. </span><br /><br /><span style=\"font-size: 12pt;\"> The appointment that needed to be held on date {appointment_date} and location {appointment_location} with contact {appointment_client_name} is cancelled.</span><br /><br /><span style=\"font-size: 12pt;\"><br />Kind Regards</span><br /><br /><span style=\"font-size: 12pt;\">{email_signature}</span>', 'appointly', 'Appointment cancelled (Sent to Staff and Attendees)', 'appointment-cancelled-to-staff');

        create_email_template('Your appointment has been cancelled !', '<span style=\"font-size: 12pt;\"> Hello {appointment_client_name}. </span><br /><br /><span style=\"font-size: 12pt;\"> The appointment that needed to be held on date {appointment_date} and location {appointment_location} is now cancelled.</span><br /><br /><span style=\"font-size:12pt;\"><br />Kind Regards</span><br /><br /><span style=\"font-size: 12pt;\">{email_signature}</span>', 'appointly', 'Appointment cancelled (Sent to Contact)', 'appointment-cancelled-to-contact');

        create_email_template('You have an upcoming appointment !', '<span style=\"font-size: 12pt;\"> Hello {appointment_client_name}. </span><br /><br /><span style=\"font-size: 12pt;\"> You have an upcoming appointment that is need to be held date {appointment_date} and location {appointment_location}.</span><br /><br /><span style=\"font-size: 12pt;\"><strong>Additional info for your appointment</strong></span><br /><span style=\"font-size: 12pt;\"><strong>Appointment Subject:</strong> {appointment_subject}</span><br /><span style=\"font-size: 12pt;\"><strong>Appointment Description:</strong> {appointment_description}</span><br /><span style=\"font-size: 12pt;\"><strong>Appointment scheduled date to start:</strong> {appointment_date}</span><br /><span style=\"font-size: 12pt;\"><strong>You can view this appointment at the following link:</strong> <a href="{appointment_public_url}">Your appointment URL</a></span><br /><span style=\"font-size: 12pt;\"><br />Kind Regards</span><br /><br /><span style=\"font-size: 12pt;\">{email_signature}</span>', 'appointly', 'Appointment reminder (Sent to Contact)', 'appointment-cron-reminder-to-contact');

        create_email_template('You are added as a appointment attendee', '<span style=\"font-size: 12pt;\"> Hello {staff_firstname} {staff_lastname}.</span><br /><br /><span style=\"font-size: 12pt;\"> You are added as a appointment attendee.</span><br /><br /><span style=\"font-size: 12pt;\"><strong>Appointment Subject:</strong> {appointment_subject}</span><br /><span style=\"font-size: 12pt;\"><strong>Appointment Description:</strong> {appointment_description}</span><br /><span style=\"font-size: 12pt;\"><strong>Appointment scheduled date to start:</strong> {appointment_date}</span><br /><span style=\"font-size: 12pt;\"><strong>You can view this appointment at the following link:</strong> <a href="{appointment_admin_url}">Your appointment URL</a></span><br /><span style=\"font-size: 12pt;\"><br />Kind Regards</span><br /><br /><span style=\"font-size: 12pt;\">{email_signature}</span>', 'appointly', 'Appointment approved (Sent to Staff and Atendees)', 'appointment-approved-to-staff');

        create_email_template('Your appointment has been approved', '<span style=\"font-size: 12pt;\"> Hello {appointment_client_name}.</span><br /><br /><span style=\"font-size: 12pt;\"> You appointment has been approved!</span><br /><br /><span style=\"font-size: 12pt;\"><strong>Appointment Subject:</strong> {appointment_subject}</span><br /><span style=\"font-size: 12pt;\"><strong>Appointment Description:</strong> {appointment_description}</span><br /><span style=\"font-size: 12pt;\"><strong>Appointment scheduled date to start:</strong> {appointment_date}</span><br /><span style=\"font-size: 12pt;\"><strong>You can keep track of your appointment at the following link:</strong> <a href="{appointment_public_url}">Your appointment URL</a></span><br /><span style=\"font-size: 12pt;\"><br/>If you have any questions Please contact us for more information.</span><br /><br /><span style=\"font-size: 12pt;\"><br />Kind Regards</span><br /><br /><span style=\"font-size: 12pt;\">{email_signature}</span>', 'appointly', 'Appointment approved (Sent to Contact)', 'appointment-approved-to-contact');

        create_email_template('New appointment request via external form', '<span 12pt=""><span 12pt="">Hello {staff_firstname} {staff_lastname}<br /><br />New appointment request submitted via external form</span>.<br /><br /><span 12pt=""><strong>Appointment Subject:</strong> {appointment_subject}</span><br /><br /><span 12pt=""><strong>Appointment Description:</strong> {appointment_description}</span><br /><br /><span 12pt=""><strong>Appointment requested scheduled start date:</strong> {appointment_date}</span><br /><br /><span 12pt=""><strong>You can view this appointment request at the following link:</strong> <a href="{appointment_admin_url}">{appointment_admin_url}</a></span><br /><br /><br />{companyname}<br />{crm_url}<br /><span 12pt=""></span></span>', 'appointly', 'New appointment request (Sent to Responsible Person)', 'appointment-submitted-to-staff');

        create_email_template('You have been assigned to handle a new callback', '<span 12pt=""><span 12pt="">Hello {staff_firstname} {staff_lastname}<br /><br />An admin assigned a callback to you, you can view this callback request at the following link:</strong> <a href="{admin_url}/appointly/callbacks">{admin_url}/appointly/callbacks</a></span><br /><br /><br />{companyname}<br />{crm_url}<br /><span 12pt=""></span></span>', 'appointly', 'Assigned to callback (Sent to Staff)', 'callback-assigned-to-staff');

        create_email_template('You have a new callback request', '<span 12pt=""><span 12pt="">Hello {staff_firstname} {staff_lastname}<br /><br />A new callback request has just been submitted, fast navigate to callbacks to view latest callback submitted:</strong> <a href="{admin_url}/appointly/callbacks">{admin_url}/appointly/callbacks</a></span><br /><br /><br />{companyname}<br />{crm_url}<br /><span 12pt=""></span></span>', 'appointly', 'New callback request (Sent to Callbacks Responsible Person)', 'newcallback-requested-to-staff');
    }
}


if (!function_exists('init_appointly_install_sequence')) {
    /**
     * Initialize tables content example data for email templates and sms in database
     */
    function init_appointly_install_sequence()
    {
        init_appointly_database_tables();
        init_appointly_template_tables();
    }
}


if (!function_exists('checkForModuleReinstallation')) {
    /** 
     * Percusion database checks
     */
    function checkForModuleReinstallation()
    {
        $CI = &get_instance();

        if (!$CI->db->field_exists('notes', db_prefix() . 'appointly_appointments')) {
            $CI->db->query("ALTER TABLE " . db_prefix() . "appointly_appointments ADD `notes` LONGTEXT NULL AFTER `address`;");
        }

        if (!$CI->db->field_exists('type_id', db_prefix() . 'appointly_appointments')) {
            $CI->db->query("ALTER TABLE " . db_prefix() . "appointly_appointments ADD `type_id` INT(11) NOT NULL DEFAULT '0' AFTER `source`;");
        }

        if (!$CI->db->field_exists('google_event_id', db_prefix() . 'appointly_appointments')) {
            $CI->db->query("ALTER TABLE " . db_prefix() . "appointly_appointments ADD `google_event_id` VARCHAR(191) NULL DEFAULT NULL AFTER `id`;");
        }

        if (!$CI->db->field_exists('google_calendar_link', db_prefix() . 'appointly_appointments')) {
            $CI->db->query("ALTER TABLE " . db_prefix() . "appointly_appointments ADD `google_calendar_link` VARCHAR(191) NULL DEFAULT NULL AFTER `google_event_id`;");
        }

        if (!$CI->db->field_exists('google_added_by_id', db_prefix() . 'appointly_appointments')) {
            $CI->db->query("ALTER TABLE " . db_prefix() . "appointly_appointments ADD `google_added_by_id` int(11) NULL DEFAULT NULL AFTER `google_calendar_link`;");
        }
    }
}
