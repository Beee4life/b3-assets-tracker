<?php $tr_class = ( count($grouped_data) ) == $row_counter ? 'totalrow' : ''; ?>
<tr class="<?php echo esc_attr( $tr_class ); ?>">
    <?php $column_counter = 1; ?>
    <?php foreach( $row as $value ) { ?>
        <?php
            $td_class = '';
            if ( 1 == $column_counter ) {
                $td_class = 'type';
            } elseif ( 1 < $column_counter ) {
                if ( strpos( $value, '-' ) !== false ) {
                    $class = 'minus';
                } elseif ( '€ 0,00' != $value ) {
                    $class = 'plus';
                } else {
                    $class = 'same';
                }

                if ( $show_total && $show_diff ) {
                    switch( $column_counter ) {
                        case $amount_cols - 2:
                            $td_class = sprintf( 'diff %s', $class );
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
                    switch( $column_counter ) {
                        case $amount_cols - 1:
                            $td_class = sprintf( 'diff %s', $class );
                            break;
                        case $amount_cols:
                            $td_class = 'diff-percent';
                            break;
                        default:
                            $td_class = '';
                    }

                } elseif ( $show_total ) {
                    switch( $column_counter ) {
                        case $amount_cols:
                            $td_class = 'percent-total';
                            break;
                        default:
                            $td_class = '';
                    }
                } else {
                    switch( $column_counter ) {
                        case $amount_cols - 1:
                            $td_class = sprintf( 'diff %s', $class );
                            break;
                        case $amount_cols:
                            $td_class = 'diff-percent';
                            break;
                        default:
                            $td_class = '';
                    }
                }
            }
        ?>
        <td class="<?php echo esc_attr( $td_class ); ?>">
            <?php
                if ( 1 == $column_counter ) {
                    echo $value;
                } else {
                    echo esc_html( $value );
                }
            ?>
        </td>
        <?php $column_counter++; ?>
    <?php } ?>
</tr>
