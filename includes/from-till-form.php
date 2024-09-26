<form name="" action="" method="post">
    <?php if ( $is_graph_page && $show_graph_options ) { ?>
        <input type="hidden" name="show_graph" value="1" />
    <?php } ?>
    <table class="data-input">
        <thead>
        <tr>
            <th>
                From
            </th>
            <th>
                Till
            </th>
            <?php if ( $show_asset_types ) { ?>
                <th>Asset type</th>
            <?php } ?>
            <?php if ( $is_graph_page ) { ?>
                <th class="">Range</th>
            <?php } ?>
            <?php if ( $is_graph_page && $show_graph_options ) { ?>
                <th>Graph type</th>
            <?php } ?>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <tr>
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
            <?php if ( $show_asset_types && $types ) { ?>
                <td>
                    <label>
                        <select name="asset_type">
                            <option value="all">All (total)</option>
                            <?php foreach( $types as $id => $type ) { ?>
                                <?php if ( bp_is_type_hidden( $type->id ) ) { ?>
                                    <?php continue; ?>
                                <?php } ?>
                                <?php echo sprintf( '<option value="%s" %s>%s</option>', $type->id, selected( $type->id, $asset_type ), $type->name ); ?>
                            <?php } ?>
                        </select>
                    </label>
                </td>
            <?php } ?>
            <?php if ( $is_dashboard ) { ?>
                <input type="hidden" name="view_range" value="1" />
            <?php } else { ?>
                <td class="">
                    <label>
                        <select name="view_range">
                            <?php foreach( $range as $id => $label ) { ?>
                                <?php echo sprintf( '<option value="%s" %s>%s</option>', $id, selected( $view_range, $id ), $label ); ?>
                            <?php } ?>
                        </select>
                    </label>
                </td>
            <?php } ?>
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
                <input type="submit" class="admin-button admin-button-small" value="Filter" />
            </td>
        </tr>
        </tbody>
    </table>
</form>
