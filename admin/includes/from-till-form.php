<form name="" action="" method="post">
    <?php if ( $is_graph_page && $show_graph_options ) { ?>
        <input type="hidden" name="show_graph" value="1" />
    <?php } ?>
    <table class="data-input">
        <thead>
        <tr>
            <?php if ( $is_graph_page && $show_graph_options ) { ?>
                <th>Graph type</th>
            <?php } ?>
            <th>
                From
            </th>
            <th>
                Till
            </th>
            <?php if ( $show_all_option ) { ?>
                <th class="checkbox">Show all</th>
            <?php } ?>
            <?php if ( $show_asset_types ) { ?>
                <th class="asset-types">Asset type(s)</th>
            <?php } ?>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <?php if ( $is_graph_page && $show_graph_options ) { ?>
                <td>
                    <label>
                        <select name="graph_type">
                            <?php foreach( $graph_options as $id => $label ) { ?>
                                <option value="<?php echo $id; ?>" <?php selected( $graph_type, $id ); ?>><?php echo $label; ?></option>
                            <?php } ?>
                        </select>
                    </label>
                </td>
            <?php } ?>
            <td>
                <label>
                    <select name="stats_from">
                        <?php echo sprintf( '<option value="">%s</option>', 'Start' ); ?>
                        <?php foreach( $all_dates as $date ) { ?>
                            <?php $show_day = 2 == gmdate( 'N', strtotime( $date ) ) ? sprintf( ' (%s)', gmdate( 'D', strtotime( $date ) ) ) : false; ?>
                            <?php echo sprintf( '<option value="%s" %s>%s%s</option>', $date, selected( $date_from, $date ), bp_format_value( $date, 'date' ), sprintf( ' (%s)', gmdate( 'D', strtotime( $date ) ) ) ); ?>
                        <?php } ?>
                    </select>
                </label>
            </td>
            <td>
                <label>
                    <select name="stats_until" required>
                        <?php echo sprintf( '<option value="">%s</option>', 'End' ); ?>
                        <?php foreach( $all_dates as $date ) { ?>
                            <?php $show_day = 2 == gmdate( 'N', strtotime( $date ) ) ? ' *' : false; ?>
                            <?php echo sprintf( '<option value="%s" %s>%s%s</option>', $date, selected( $date_until, $date ), bp_format_value( $date, 'date' ), sprintf( ' (%s)', gmdate( 'D', strtotime( $date ) ) ) ); ?>
                        <?php } ?>
                    </select>
                </label>
            </td>
            <?php if ( $show_all_option ) { ?>
                <td class="checkbox">
                    <label>
                        <input type="checkbox" name="show_all" value="1">
                    </label>
                </td>
            <?php } ?>
            <?php if ( $show_asset_types && $types ) { ?>
                <td class="asset-types">
                    <div id="list1" class="dropdown-check-list" tabindex="100">
                        <div class="anchor">Select Type(s) &darr;</div>
                        <ul class="items">
                            <?php foreach( $types as $id => $type ) { ?>
                                <?php if ( bp_is_type_hidden( $type->id ) ) { ?>
                                    <?php continue; ?>
                                <?php } ?>
                                <li>
                                    <label>
                                        <?php $checked = ''; ?>
                                        <?php if ( is_array( $asset_types ) && in_array( $type->id, $asset_types ) ) { ?>
                                            <?php $checked = ' checked="checked"'; ?>
                                        <?php } ?>
                                        <?php echo sprintf( '<input type="checkbox" name="asset_type[]" value="%s" %s>%s</input>', $type->id, $checked, $type->name ); ?>
                                    </label>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </td>
            <?php } ?>
            <td>
                <input type="submit" class="admin-button admin-button-small" value="Filter" />
            </td>
        </tr>
        </tbody>
    </table>
</form>
