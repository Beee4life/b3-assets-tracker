<?php
    include 'forms/form--add-type.php';
    include 'forms/form--delete-type.php';
    include 'forms/form--add-data.php';
    include 'forms/form--delete-data.php';
    include 'forms/form--settings.php';
    
    function bp_validate_form_input( $post_data = [] ) {
        if ( ! isset( $post_data[ 'bp_date' ] ) || empty( $post_data[ 'bp_date' ] ) ) {
            return [
                'code'    => 'error_no_date',
                'message' => __( 'No date selected.', 'b3-assets-tracker' ),
            ];
        }
        
        return true;
    }
