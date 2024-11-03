<?php
    /**
     * Content for the 'data input page'
     */
    function bp_assets_add_data() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html( __( 'Sorry, you do not have sufficient permissions to access this page.', 'b3-assets-tracker' ) ) );
        }
        $action       = admin_url( 'admin.php?page=bp-assets-dashboard' );
        $edit_date    = isset( $_GET[ 'date' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'date' ] ) ) : '';
        $grouped_data = [];
        $max_date     = gmdate( 'Y-m-d', ( time() + WEEK_IN_SECONDS ) );
        $types        = bp_get_asset_types();

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
                <?php echo esc_html( get_admin_page_title() ); ?>
            </h1>

            <?php
                if ( function_exists( 'bp_show_error_messages' ) ) {
                    bp_show_error_messages();
                }

                do_action( 'bp_admin_menu' );
            ?>

            <?php if ( $types ) { ?>
                <div id="data-input">
                    <form name="add-data" action="<?php echo esc_url_raw( $action ); ?>" method="post">
                        <input name="add_data_nonce" type="hidden" value="<?php echo esc_attr( wp_create_nonce( 'add-data-nonce' ) ); ?>" />
                        <?php if ( $edit_date ) { ?>
                            <input name="update_data" type="hidden" value="<?php echo esc_attr( $edit_date ); ?>" />
                        <?php } ?>
                        <table class="add-data">
                            <tr>
                                <th>
                                    <?php echo esc_html__( 'Date', 'b3-assets-tracker' ); ?>
                                </th>
                                <td>
                                    <label>
                                        <input name="bp_date" type="date" class="" min="2024-07-30" value="<?php echo esc_attr( $edit_date ); ?>" max="<?php echo esc_attr( $max_date ); ?>" required />
                                    </label>
                                </td>
                                <td>
                                    <b>
                                        <?php echo esc_html__( 'Status', 'b3-assets-tracker' ); ?>
                                    </b>
                                </td>
                            </tr>
                            <?php foreach( $types as $type ) { ?>
                                <tr>
                                    <th>
                                        <?php echo esc_html( $type->name ); ?>
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
                                            <input name="bp_value[<?php echo esc_attr( $type->id ); ?>]" type="number" class="" placeholder="Value" step="0.01" value="<?php echo esc_attr( $value ); ?>" />
                                        </label>
                                    </td>
                                    <td>
                                        <?php
                                            if ( ! empty( $type->hide ) ) {
                                                echo esc_html__( 'Hidden', 'b3-assets-tracker' );
                                            }
                                            if ( ! empty( $type->hide ) && ! empty( $type->closed ) && '0000-00-00' !== $type->closed ) {
                                                echo ' & ';
                                            }
                                            if ( ! empty( $type->closed ) && '0000-00-00' !== $type->closed ) {
                                                echo esc_html__( 'Closed', 'b3-assets-tracker' );
                                            }
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                        <br>
                        <input type="submit" class="" value="<?php esc_attr_e( 'Save', 'b3-assets-tracker' ); ?>" />
                    </form>
                </div>
            <?php } else { ?>
                <a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=bp-assets-types' ) ); ?>">
                    <?php esc_html_e( 'Add types first', 'b3-assets-tracker' ); ?>
                </a>
            <?php } ?>
        </div>
    <?php }
