<?php
    /**
     * Content for the 'dashboard page'
     */
    function bp_assets_settings() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html( __( 'Sorry, you do not have sufficient permissions to access this page.', 'bpnl' ) ) );
        }
        
        $stored_date_format    = get_option( 'bp_date_format' );
        $stored_date_separator = get_option( 'bp_date_separator', '-' );
        $stored_currency       = get_option( 'bp_currency' );
        
        $currencies = [
            '€',
            '$',
            '£',
            '¥',
        ];

        $date_separators = [
            '-',
            '.',
            '/',
        ];

        $date_formats = [
            sprintf( 'd%sm', $stored_date_separator ),
            sprintf( 'd%sm%sy', $stored_date_separator, $stored_date_separator ),
            sprintf( 'd%sm%sY', $stored_date_separator, $stored_date_separator ),
            sprintf( 'j%sn', $stored_date_separator ),
            sprintf( 'j%sn%sy', $stored_date_separator, $stored_date_separator ),
            sprintf( 'j%sn%sY', $stored_date_separator, $stored_date_separator ),
            'j F',
            'j F, Y',
        ];

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

            <div id="data-input">
                <form name="add-settings" action="" method="post">
                    <input name="assets_settings_nonce" type="hidden" value="<?php echo wp_create_nonce( 'assets-settings-nonce' ); ?>" />
                    <table class="settings">
                        <tr>
                            <th>
                                Currency
                            </th>
                            <td>
                                <label>
                                    <select name="bp_currency">
                                        <?php foreach( $currencies as $currency ) { ?>
                                            <?php echo sprintf( '<option value="%s" %s>%s</option>', $currency, selected( $currency, $stored_currency ), $currency ); ?>
                                        <?php } ?>
                                    </select>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Date separator
                            </th>
                            <td>
                                <label>
                                    <select name="bp_date_separator">
                                        <?php foreach( $date_separators as $separator ) { ?>
                                            <?php echo sprintf( '<option value="%s" %s>%s</option>', $separator, selected( $separator, $stored_date_separator ), $separator ); ?>
                                        <?php } ?>
                                    </select>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Date format
                            </th>
                            <td>
                                <label>
                                    <select name="bp_date_format">
                                        <?php foreach( $date_formats as $format ) { ?>
                                            <?php echo sprintf( '<option value="%s" %s>%s</option>', $format, selected( $format, $stored_date_format ), gmdate( $format, time() ) ); ?>
                                        <?php } ?>
                                    </select>
                                </label>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <input type="submit" class="admin-button admin-button-small" />
                </form>
            </div>

        </div>
    <?php }
