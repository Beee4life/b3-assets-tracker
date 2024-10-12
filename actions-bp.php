<?php
    function bp_add_graph( $add_graph = false ) {
        if ( $add_graph ) {
            echo '<div id="chart_div"></div>';
        }
    }
    add_action( 'add_graph', 'bp_add_graph' );
