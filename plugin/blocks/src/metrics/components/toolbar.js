/**
 * WordPress dependencies
 *
 */
import { __ } from '@wordpress/i18n';
/**
 * Internal dependencies
 */
import RangePicker from './rangePicker';

const identity = (v) => v;

export default ({ interval, onIntervalUpdate = identity, onRefresh = identity }) => {
	return (
		<div className="wpcloud-metrics-toolbar">
			<RangePicker interval={interval} onIntervalUpdate={onIntervalUpdate} />
			<button onClick={onRefresh} className="wpcloud-metrics-toolbar__refresh">
				{__('Refresh')}
			</button>
		</div>
	);
}