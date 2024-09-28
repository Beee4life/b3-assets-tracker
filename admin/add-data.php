<?php
    /**
     * Content for the 'data input page'
     */
    function bp_assets_add_data() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html( __( 'Sorry, you do not have sufficient permissions to access this page.', 'bpnl' ) ) );
        }
        $action       = admin_url( 'admin.php?page=bp-assets-dashboard' );
        $edit_date    = isset( $_GET[ 'date' ] ) ? $_GET[ 'date' ] : '';
        $grouped_data = [];
        $max_date     = gmdate( 'Y-m-d', ( time() + WEEK_IN_SECONDS ) );
        $types        = bp_get_types();
        
        if ( $edit_date ) {
            $data         = bp_get_data( $edit_date );
            $grouped_data = $data;

        } elseif ( ! empty( $data ) ) {
            foreach( $data as $row ) {
                if ( ! array_key_exists( $row->date, $grouped_data ) ) {
                    $grouped_data[ $row->date ] = [];
                }
                $grouped_data[ $row->date ][] = $row;
            }
        }
        ?>

        <div id="wrap">

            <h1>
                <?php echo get_admin_page_title(); ?>
            </h1>

            <?php
                if ( function_exists( 'bp_show_error_messages' ) ) {
                    bp_show_error_messages();
                }
            ?>
            
            <?php echo B3AssetsTracker::bp_admin_menu(); ?>

            <?php if ( $types ) { ?>
                <div id="data-input">
                    <form name="add-data" action="<?php echo $action; ?>" method="post">
                        <input name="add_data_nonce" type="hidden" value="<?php echo wp_create_nonce( 'add-data-nonce' ); ?>" />
                        <?php if ( $edit_date ) { ?>
                            <input name="update_data" type="hidden" value="<?php echo $edit_date; ?>" />
                        <?php } ?>
                        <table class="add-data">
                            <tr>
                                <th>
                                    Date
                                </th>
                                <td>
                                    <label>
                                        <input name="bp_date" type="date" class="" min="2024-07-30" value="<?php echo $edit_date; ?>" max="<?php echo $max_date; ?>" required />
                                    </label>
                                </td>
                            </tr>
                            <?php foreach( $types as $type ) { ?>
                                <tr>
                                    <th>
                                        <?php echo $type->name; ?>
                                    </th>
                                    <td>
                                        <label>
                                            <?php
                                                $value = '';
                                                if ( $edit_date && $grouped_data ) {
                                                    foreach( $grouped_data as $item ) {
                                                        if ( $type->id == $item->type ) {
                                                            if ( '0.00' !== $item->value ) {
                                                                $value = $item->value;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                }
                                            ?>
                                            <input name="bp_value[<?php echo $type->id; ?>]" type="number" class="" placeholder="Value" step="0.01" value="<?php echo $value; ?>" />
                                        </label>
                                    </td>
                                    <td>
                                        <?php echo ! empty( $type->hide ) ? 'Hidden' : ''; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                        <br>
                        <input type="submit" class="admin-button admin-button-small" />
                    </form>
                </div>
            <?php } else { ?>
                <a href="<?php echo admin_url( 'admin.php?page=bp-assets-types' ); ?>">
                    Add types first
                </a>
            <?php } ?>
        </div>
    <?php }
