<?php
    add_action( 'wp_head', 'bp_add_chart_script' );

    function bp_add_graph( $add_graph = false ) {
        if ( $add_graph ) {
            echo '<div id="chart_div"></div>';
            echo '';
        }
    }
    add_action( 'add_graph', 'bp_add_graph' );
