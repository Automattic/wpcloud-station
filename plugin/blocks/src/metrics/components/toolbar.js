/**
 * WordPress dependencies
 *
 */
import { Flex } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
/**
 * Internal dependencies
 */
import RangePicker from './rangePicker';
import Filters from './filters';


export default ({ interval, onIntervalUpdate, onFiltersUpdate, onRefresh, filters = [] }) => {
	return (
		<Flex direction="column" gap="2" className="wpcloud-metrics-toolbar">
			<Flex>
				<Filters onFiltersUpdate={onFiltersUpdate} filters={filters} />
			</Flex>
			<Flex align="normal" gap="4">
				<RangePicker interval={interval} onIntervalUpdate={onIntervalUpdate} />
				<button onClick={onRefresh} className="wpcloud-metrics-toolbar__refresh">
					{__('Refresh')}
				</button>
			</Flex>
		</Flex>
	);
}
