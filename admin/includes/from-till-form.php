<form name="" action="" method="post">
    <input type="hidden" name="b3_from_till_nonce" value="<?php echo esc_attr( wp_create_nonce( 'b3-from-till-nonce' ) ); ?>" />
    <?php if ( $show_graph_options ) { ?>
        <input type="hidden" name="show_graph" value="1" />
    <?php } ?>
    <table class="data-input">
        <thead>
        <tr>
            <?php if ( ! empty( $graph_options ) && $show_graph_options ) { ?>
                <th>
                    <?php esc_html_e( 'Graph type', 'b3-assets-tracker' ); ?>
                </th>
            <?php } ?>
            <th>
                <?php esc_html_e( 'From', 'b3-assets-tracker' ); ?>
            </th>
            <th>
                <?php esc_html_e( 'Until', 'b3-assets-tracker' ); ?>
            </th>
            <?php if ( $show_all_option ) { ?>
                <th class="checkbox">
                    <?php esc_html_e( 'Show all', 'b3-assets-tracker' ); ?>
                </th>
            <?php } ?>
            <?php if ( $show_asset_types ) { ?>
                <th class="asset-types">
                    <?php esc_html_e( 'Asset type(s)', 'b3-assets-tracker' ); ?>
                </th>
            <?php } ?>
            <?php if ( $show_asset_groups ) { ?>
                <th class="asset-groups">
                    <?php esc_html_e( 'Asset group(s)', 'b3-assets-tracker' ); ?>
                </th>
            <?php } ?>
            <th class="submit">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <?php if ( ! empty( $graph_options ) && $show_graph_options ) { ?>
                <td>
                    <label>
                        <select name="graph_type">
                            <?php foreach( $graph_options as $id => $label ) { ?>
                                <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $graph_type, $id ); ?>><?php echo esc_html( $label ); ?></option>
                            <?php } ?>
                        </select>
                    </label>
                </td>
            <?php } ?>
            <td>
                <label>
                    <select name="stats_from">
                        <?php echo sprintf( '<option value="">%s</option>', esc_attr__( 'From', 'b3-assets-tracker' ) ); ?>
                        <?php foreach( $all_dates as $date ) { ?>
                            <?php echo sprintf( '<option value="%s" %s>%s%s</option>', esc_attr( $date ), selected( $date_from, $date ), esc_html( bp_format_value( $date, 'date' ) ), sprintf( ' (%s)', esc_html( gmdate( 'D', strtotime( $date ) ) ) ) ); ?>
                        <?php } ?>
                    </select>
                </label>
            </td>
            <td>
                <label>
                    <select name="stats_until" required>
                        <?php echo sprintf( '<option value="">%s</option>', esc_attr__( 'Until', 'b3-assets-tracker' ) ); ?>
                        <?php foreach( $all_dates as $date ) { ?>
                            <?php echo sprintf( '<option value="%s" %s>%s%s</option>', esc_attr( $date ), selected( $date_until, $date ), esc_html( bp_format_value( $date, 'date' ) ), sprintf( ' (%s)', esc_html( gmdate( 'D', strtotime( $date ) ) ) ) ); ?>
                        <?php } ?>
                    </select>
                </label>
            </td>
            <?php if ( $show_all_option ) { ?>
                <td class="checkbox">
                    <label>
                        <input type="checkbox" name="show_all" value="1"<?php checked( $show_all ); ?>>
                    </label>
                </td>
            <?php } ?>
            <?php if ( $show_asset_types && $asset_types ) { ?>
                <td class="asset-types">
                    <div id="asset-types" class="dropdown-check-list" tabindex="100">
                        <div class="anchor">
                            <?php esc_html_e( 'Select Type(s)', 'b3-assets-tracker' ); ?> &darr;
                        </div>
                        <ul class="items">
                            <?php foreach( $asset_types as $id => $name ) { ?>
                                <?php if ( 'all' != $id && bp_is_type_hidden( $id ) ) { ?>
                                    <?php continue; ?>
                                <?php } ?>
                                <li>
                                    <label>
                                        <?php $checked = ''; ?>
                                        <?php if ( is_array( $selected_asset_types ) && in_array( $id, $selected_asset_types ) ) { ?>
                                            <?php $checked = ' checked="checked"'; ?>
                                        <?php } ?>
                                        <?php echo sprintf( '<input type="checkbox" name="asset_type[]" value="%s" %s>%s</input>', esc_attr( $id ), esc_attr( $checked ), esc_html( $name ) ); ?>
                                    </label>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </td>
            <?php } ?>
            <?php if ( $show_asset_groups && $asset_groups ) { ?>
                <td class="asset-groups">
                    <div id="asset-groups" class="dropdown-check-list" tabindex="100">
                        <div class="anchor">
                            <?php esc_html_e( 'Select Group(s)', 'b3-assets-tracker' ); ?> &darr;
                        </div>
                        <ul class="items">
                            <?php foreach( $asset_groups as $id => $label ) { ?>
                                <li>
                                    <label>
                                        <?php $checked = ''; ?>
                                        <?php if ( is_array( $selected_asset_groups ) && in_array( $id, $selected_asset_groups ) ) { ?>
                                            <?php $checked = ' checked="checked"'; ?>
                                        <?php } ?>
                                        <?php echo sprintf( '<input type="checkbox" name="asset_group[]" value="%s" %s>%s</input>', esc_attr( $id ), esc_attr( $checked ), esc_html( $label ) ); ?>
                                    </label>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </td>
            <?php } ?>
            <td class="submit">
                <input type="submit" class="" value="<?php esc_attr_e( 'Filter', 'b3-assets-tracker' ); ?>" />
            </td>
        </tr>
        </tbody>
    </table>
</form>
