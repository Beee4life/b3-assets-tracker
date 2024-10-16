<?php
    include 'forms/form--add-type.php';
    include 'forms/form--add-data.php';
    include 'forms/form--settings.php';
    
    function bp_validate_form_input( $post_data = [] ) {
        if ( ! isset( $post_data[ 'bp_date' ] ) || empty( $_POST[ 'bp_date' ] ) ) {
            return [
                'code'    => 'error_no_date',
                'message' => esc_html( __( 'No date selected.', 'assets-tracker' ) ),
            ];
        }
        
        return true;
    }
