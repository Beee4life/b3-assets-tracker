<div class="graphs-help">
    <ul>
        <li class="graph-info">
            <div class="graph-info__header">
                <?php esc_html_e( 'Line chart', 'b3-assets-tracker' ); ?>
            </div>

            <ul>
                <li>
                    <?php esc_html_e( 'Choose either asset types or groups, not both.', 'b3-assets-tracker' ); ?>
                </li>
                <li>
                    <?php esc_html_e( 'Start and end date are required.', 'b3-assets-tracker' ); ?>
                </li>
            </ul>
        </li>
        <li class="graph-info">
            <div class="graph-info__header">
                <?php esc_html_e( 'Pie chart', 'b3-assets-tracker' ); ?>
            </div>
            <ul>
                <li>
                    <?php esc_html_e( "Asset group/type is ignored (because it's defines by graph type).", 'b3-assets-tracker' ); ?>
                </li>
                <li>
                    <?php esc_html_e( 'End date is required.', 'b3-assets-tracker' ); ?>
                </li>
            </ul>
        </li>
    </ul>
</div>
