/**
 * Internal dependencies
 */
import RangePicker from './rangePicker';

export default ({ interval, onIntervalUpdate = () => { } }) => {
	return (
		<div className="wpcloud-metrics-toolbar">
			<RangePicker interval={interval} onIntervalUpdate={onIntervalUpdate} />
		</div>
	);
}