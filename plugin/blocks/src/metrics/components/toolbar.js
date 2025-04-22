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
import FiltersForm from './filtersForm';

const identity = (v) => v;

export default ({ interval, onIntervalUpdate = identity, onFilterUpdate = identity, onRefresh = identity }) => {
	return (
		<Flex direction="column" gap="2" className="wpcloud-metrics-toolbar">
			<Flex align="normal" gap="4">
				<RangePicker interval={interval} onIntervalUpdate={onIntervalUpdate} />
				<button onClick={onRefresh} className="wpcloud-metrics-toolbar__refresh">
					{__('Refresh')}
				</button>
			</Flex>
			<Flex>
				<FiltersForm onFilterUpdate={onFilterUpdate} />
			</Flex>
		</Flex>
	);
}
