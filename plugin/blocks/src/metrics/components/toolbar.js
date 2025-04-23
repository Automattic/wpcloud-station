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

const identity = (v) => v;

export default ({ interval, onIntervalUpdate = identity, onFiltersUpdate = identity, onRefresh = identity }) => {
	return (

		<Flex direction="column" gap="2" className="wpcloud-metrics-toolbar">
			<Flex>
				<Filters onFiltersUpdate={(filters) => {
					console.log('Toolbar received filter update:', filters);
					// Make sure we're passing the filters object correctly to the parent component
					onFiltersUpdate(filters);
					console.log('Toolbar called onFiltersUpdate with:', filters);
				}
				} />
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
