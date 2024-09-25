<?php
    function bp_format_value( $value, $type = 'price' ) {
        if ( $type ) {
            switch ( $type ) {
                case 'date':
                    $value = gmdate( get_option( 'bp_assets_date_format' ), strtotime( $value ) );
                    break;
                case 'percent':
                    $value = sprintf( '%s %%', number_format( $value, get_option( 'bp_decimals' ), ',', '.' ) );
                    break;
                case 'price':
                    $value = sprintf( '%s %s', get_option( 'bp_currency' ), number_format( $value, 2, ',', '.' ) );
                    break;
            }
        }
        
        return $value;
    }
