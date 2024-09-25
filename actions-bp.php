<?php
    function bp_add_graph( $data = [] ) {
        if ( $data ) {
            echo '<div id="chart_div"></div>';
        }
    }
    add_action( 'add_graph', 'bp_add_graph' );
