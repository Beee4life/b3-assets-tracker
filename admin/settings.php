<?php
    /**
     * Content for the 'dashboard page'
     */
    function bp_assets_settings() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html( __( 'Sorry, you do not have sufficient permissions to access this page.', 'b3-assets-tracker' ) ) );
        }

        $dash               = '-';
        $period             = '.';
        $slash              = '/';
        $stored_date_format = get_option( 'bp_date_format' );
        $stored_currency    = get_option( 'bp_currency' );

        $currencies = [
            '€',
            '$',
            '£',
            '¥',
        ];

        $date_separators = [
            $dash,
            $slash,
            $period
        ];

        $date_formats = [];
        foreach( $date_separators as $date_separator ) {
            $date_formats[ $date_separator ] = [
                sprintf( 'd%sm', $date_separator ),
                sprintf( 'd%sm%sy', $date_separator, $date_separator ),
                sprintf( 'j%sn', $date_separator ),
                sprintf( 'j%sn%sy', $date_separator, $date_separator ),
            ];
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

            <div id="data-input">
                <form name="add-settings" action="" method="post">
                    <input name="assets_settings_nonce" type="hidden" value="<?php echo esc_html( wp_create_nonce( 'assets-settings-nonce' ) ); ?>" />
                    <table class="settings">
                        <tr>
                            <th>
                                <?php esc_html_e( 'Currency', 'b3-assets-tracker' ); ?>
                            </th>
                            <td>
                                <label>
                                    <select name="bp_currency">
                                        <?php foreach( $currencies as $currency ) { ?>
                                            <?php echo sprintf( '<option value="%s" %s>%s</option>', esc_attr( $currency ), selected( $currency, $stored_currency ), esc_html( $currency ) ); ?>
                                        <?php } ?>
                                    </select>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php esc_html_e( 'Date format', 'b3-assets-tracker' ); ?>
                            </th>
                            <td>
                                <label>
                                    <select name="bp_date_format">
                                        <?php foreach( $date_formats as $separator => $optgroup ) { ?>
                                            <optgroup label="<?php echo esc_attr( $separator ); ?>">
                                                <?php foreach( $optgroup as $option ) { ?>
                                                    <?php echo sprintf( '<option value="%s" %s>%s</option>', esc_attr( $option ), selected( $option, $stored_date_format ), esc_html( gmdate( $option, time() ) ) ); ?>
                                                <?php } ?>
                                            </optgroup>
                                        <?php } ?>
                                    </select>
                                </label>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <input name="" type="submit" value="<?php esc_attr_e( 'Save', 'b3-assets-tracker' ); ?>" />
                </form>
            </div>

        </div>
    <?php }
