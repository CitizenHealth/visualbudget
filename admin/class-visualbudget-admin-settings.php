<?php

/**
 * This class is responsible for the VB settings, defined and managed
 * using the WordPress Settings API.
 */
class VisualBudget_Admin_Settings {

    /**
     * The option group and field names are defined here
     * so that the admin class can retrieve them in order
     * to know which $_FILES[] to look for during uploading.
     */
    private $upload_field_name;
    private $url_field_name;
    private $dataset_tab_group_name;

    /**
     * Initialize the class and set a few properties.
     */
    public function __construct() {
        $this->dataset_tab_group_name = 'visualbudget_tab_datasets';
        $this->upload_field_name = 'upload';
        $this->url_field_name = 'url';
    }

    /**
     * Register and add settings, group by group.
     */
    public function register_settings() {
        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * CONFIGURATION OPTIONS
         * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

        // And add a new setting section for configuration
        add_settings_section(
            'visualbudget_config',                // section ID
            'Required configuration',             // section title
            '',                                   // callback
            'visualbudget_tab_config'             // page
        );

        // Add the name setting
        add_settings_field(
            'org_name',                                      // setting ID
            'Name of city, town, district, or organization', // setting title
            array( $this, 'org_name_callback' ),             // callback function
            'visualbudget_tab_config',                       // page
            'visualbudget_config'                            // settings section
        );

        // Add the contact email setting
        add_settings_field(
            'contact_email',
            'Contact email address',
            array( $this, 'contact_email_callback' ),
            'visualbudget_tab_config',
            'visualbudget_config'
        );

        // Now register the settings
        register_setting(
            'visualbudget_tab_config',            // option group
            'visualbudget_tab_config',            // option name
            array( $this, 'sanitize' )            // sanitize
        );

        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * DATASET UPLOADER
         * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

        // And add a new setting section for the uploader
        add_settings_section(
            'visualbudget_upload',                // section ID
            '',                                   // section title
            '',                                   // callback
            $this->dataset_tab_group_name         // page
        );

        // Add the contact email setting
        add_settings_field(
            $this->upload_field_name,
            'Upload new dataset',
            array( $this, 'upload_callback' ),
            $this->dataset_tab_group_name,
            'visualbudget_upload'
        );

        // Add the contact email setting
        add_settings_field(
            $this->url_field_name,
            'Add dataset from URL',
            array( $this, 'url_callback' ),
            $this->dataset_tab_group_name,
            'visualbudget_upload'
        );

        // Now register the settings
        register_setting(
            $this->dataset_tab_group_name,        // option group
            $this->dataset_tab_group_name,        // option name
            array( $this, 'sanitize' )            // sanitize
        );
    }

    /**
     * Sanitize each setting field as needed
     * @param array     $input      Contains all settings fields as array keys
     */
    public function sanitize( $input ) {
        $new_input = array();
        if( isset( $input['org_name'] ) )
            $new_input['org_name'] = sanitize_text_field( $input['org_name'] );

        if( isset( $input['contact_email'] ) )
            $new_input['contact_email'] = sanitize_text_field( $input['contact_email'] );

        // We don't want WP to save uploaded files to the database;
        // so we intercept them in the admin and upload them ourselves locally.
        if( isset( $input['upload'] ) ) {
            // Do nothing.
        }

        // We won't save this URL forever, but we will keep it saved until
        // the dataset is retrieved, validated, & saved locally. This way we
        // know the last URL fetched, which is useful e.g. for displaying
        // retrieval errors.
        if( isset( $input['url'] ) ) {
            $new_input['url'] = esc_url_raw( $input['url'] );
        }

        return $new_input;
    }

    // Callback for the organization name setting
    public function org_name_callback() {
        printf(
            '<input type="text" size="35" id="org_name" name="visualbudget_tab_config[org_name]" value="%s" />',
            isset( $this->options['org_name'] ) ? esc_attr( $this->options['org_name']) : ''
        );
    }

    // Callback for the contact email setting
    public function contact_email_callback() {
        printf(
            '<input type="text" size="35" id="contact_email" name="visualbudget_tab_config[contact_email]" value="%s" />',
            isset( $this->options['contact_email'] ) ? esc_attr( $this->options['contact_email']) : ''
        );
    }

    // Callback for the uploader
    public function upload_callback() {
        printf( '<input name="%s[upload]" id="upload" type="file" />',
                $this->dataset_tab_group_name );
    }

    // Callback for the uploader
    public function url_callback() {
        printf( '<input type="text" size="55" id="url" name="%s[url]" value="" />',
                $this->dataset_tab_group_name );
    }

    // Get function for the upload settings group name.
    // The admin class uses this.
    public function get_dataset_tab_group_name() {
        return $this->dataset_tab_group_name;
    }

    // Get function for the upload field names.
    // The admin class uses this.
    public function get_upload_field_name() {
        return $this->upload_field_name;
    }

    // Get function for the URL field name.
    // The admin class uses this.
    public function get_url_field_name() {
        return $this->url_field_name;
    }

}
