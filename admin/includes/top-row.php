<thead>
<tr class="toprow">
    <?php $column_counter = 1; ?>
    <?php foreach( $row as $value ) { ?>
        <?php
            $td_class = '';
            if ( 0 < $column_counter ) {
                if ( $show_diff && $show_total ) {
                    switch($column_counter) {
                        case 1:
                            $td_class = 'type';
                            break;
                        case $amount_cols - 2:
                            $td_class = 'diff';
                            break;
                        case $amount_cols - 1:
                            $td_class = 'diff-percent';
                            break;
                        case $amount_cols:
                            $td_class = 'percent-total';
                            break;
                        default:
                            $td_class = '';
                    }
                    
                } elseif ( $show_diff ) {
                    switch($column_counter) {
                        case 1:
                            $td_class = 'type';
                            break;
                        case $amount_cols - 1:
                            $td_class = 'diff';
                            break;
                        case $amount_cols:
                            $td_class = 'diff-percent';
                            break;
                        default:
                            $td_class = '';
                    }
                    
                } elseif ( $show_total ) {
                    switch($column_counter) {
                        case 1:
                            $td_class = 'type';
                            break;
                        case $amount_cols:
                            $td_class = 'percent-total';
                            break;
                        default:
                            $td_class = '';
                    }
                }
            }
        ?>
        <th class="<?php echo $td_class; ?>">
            <?php
                if ( 1 == $column_counter ) {
                    echo $value;
                } elseif ( 1 < $column_counter ) {
                    $edit_url = admin_url( sprintf( 'admin.php?page=bp-assets-add-data&date=%s', $value ) );
                    if ( $show_diff && $show_total ) {
                        if ( $column_counter < ( $amount_cols - 2 ) ) {
                            $week_day = gmdate( 'l', strtotime( $value ) );
                            $value    = bp_format_value( $value, 'date' );

                            if ( current_user_can( 'manage_options' ) ) {
                                echo sprintf( '<a href="%s">%s</a>', $edit_url, $value );
                            } else {
                                echo $value;
                            }
                        } else {
                            echo $value;
                        }
                    
                    } elseif ( $show_diff ) {
                        if ( $column_counter < ( $amount_cols - 1 ) ) {
                            $value = bp_format_value( $value, 'date' );
                            if ( current_user_can( 'manage_options' ) ) {
                                echo sprintf( '<a href="%s">%s</a>', $edit_url, $value );
                            } else {
                                echo $value;
                            }
                        } else {
                            echo $value;
                        }
                        
                    } elseif ( $show_total ) {
                        if ( $column_counter == $amount_cols ) {
                            echo 'Total %';
                        } else {
                            $week_day = gmdate( 'D', strtotime( $value ) );
                            $value    = bp_format_value( $value, 'date' );

                            if ( current_user_can( 'manage_options' ) ) {
                                echo sprintf( '<a href="%s">%s</a>', $edit_url, $value );
                            } else {
                                echo $value;
                            }
                        }

                    } elseif ( ! $show_diff && 1 < $column_counter ) {
                        $value = bp_format_value( $value, 'date' );
                        if ( current_user_can( 'manage_options' ) ) {
                            echo sprintf( '<a href="%s">%s</a>', $edit_url, $value );
                        } else {
                            echo $value;
                        }
                    } else {
                        echo $value;
                    }
                }
            ?>
        </th>
        <?php $column_counter++; ?>
    <?php } ?>
</tr>
</thead>
