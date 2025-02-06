/**
 * External dependencies
 */
import { useState, useRef, useEffect } from 'react';

/**
 * WordPress dependencies
 */
import { __, _n, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import BoundaryInput from './boundaryInput';
import { isValidDate, getFromNow, parseRelativeTime } from '../utils';

const rangeOptions = {
	'': '',
	'now-5m': __('Last 5 Minutes'),
	'now-15m': __('Last 15 Minutes'),
	'now-30m': __('Last 30 Minutes'),
	'now-1h': __('Last 1 Hour'), // default
	'now-3h': __('Last 3 Hours'),
	'now-6h': __('Last 6 Hours'),
	'now-12h': __('Last 12 Hours'),
	'now-2d': __('Last 2 Days'),
	'now-7d': __('Last 7 Days'),
	'now-30d': __('Last 30 Days'),
	'now-90d': __('Last 90 Days'),
	'now-6M': __('Last 6 Months'),
}

const units = {
	's': __('second'),
	'm': __('minute'),
	'h': __('hour'),
	'd': __('day'),
	'w': __('week'),
	'M': __('month'),
}

export default ({ interval, onIntervalUpdate = () => { } }) => {
	const detailsRef = useRef(null);

	const [start, setStart] = useState('now-1h');
	const [end, setEnd] = useState('now');

	const [startErrorMessage, setStartErrorMessage] = useState('');
	const [endErrorMessage, setEndErrorMessage] = useState('');
	const [startAfterEndError, setStartAfterEndError] = useState(false);

	const [summaryText, setSummaryText] = useState('Last 1 Hour');
	const [openedCalRef, setOpenedCalRef] = useState(null);

	// @TODO: figure out how to reset this when using the inputs
	const [rangeOptionValue, setRangeOptionValue] = useState('now-1h');

	const [refresh, setRefresh] = useState(false);

	useEffect(() => {
		setStart(interval.start || 'now-1h');
		setEnd(interval.end || 'now');
	}, [interval]);

	// Effects
	// Bind dom events
	useEffect(() => {
		// Allow enter to trigger a refresh
		const handleKeyDown = (event) => {
			if (event.key === 'Enter') {
				const detailsElement = detailsRef.current;
				if (detailsElement && detailsElement.hasAttribute('open')) {
					setRefresh(true);
					onFilter();
				}
			}
		};
		document.addEventListener('keydown', handleKeyDown);

		// Clean up after the details element is closed
		const handleDetailsToggle = () => {
			if (detailsRef.current.hasAttribute('open')) {
				setOpenedCalRef(null);
			}
		};

		detailsRef?.current?.addEventListener('toggle', handleDetailsToggle);

		return () => {
			document.removeEventListener('keydown', handleKeyDown);
			detailsRef?.current?.removeEventListener('toggle', handleDetailsToggle);
		};
	}, [start, end, detailsRef]);

	// Refresh the data when the refresh state is set
	useEffect(() => {
		if (refresh) {
			onFilter();
			setRefresh(false);
		}
	}, [refresh, start, end]);

	// Initialize the summary message
	useEffect(() => {
		buildSummaryMessage(start, end);
	}, [start, end]);

	const buildSummaryMessage = (start, end) => {
		let from, to;
		if (!isValidDate(start) || !isValidDate(end)) {
			return;
		}
		let newSummary = summaryText;
		const [endNow, endAmount, endUnit] = getFromNow(end);
		const [startNow, startAmount, startUnit] = getFromNow(start);

		if (endNow && startNow) {

			// If there's no end amount, the build a "quick" summary.
			if (!endAmount) {
				if (!startUnit) {
					return;
				}
				newSummary = sprintf(
					_n(`Last %d %s`, `Last %d %ss`, parseInt(startAmount, 10), 'wpcloud-metrics-toolbar'),
					startAmount,
					units[startUnit]
				);
			} else {
				from = parseRelativeTime(start);
				to = parseRelativeTime(end);
				newSummary = `${from} → ${to}`;
			}

		} else {
			from = start.replace('T', ' ');
			to = end.replace('T', ' ');
			newSummary = `${from} → ${to}`;
		}
		setSummaryText(newSummary);
	}

	const onFilter = () => {
		let isValid = true;

		const newStart = new Date(parseRelativeTime(start) || start);
		const newEnd = new Date(parseRelativeTime(end) || end);

		if (isNaN(newStart.getTime())) {
			setStartErrorMessage(__('Invalid start date'));
			isValid = false;
		}

		if (isNaN(newEnd.getTime())) {
			setEndErrorMessage(__('Invalid end date'));
			isValid = false;
		}

		if (newStart > newEnd) {
			setStartAfterEndError(true);
			setStartErrorMessage(__('Start date must be before end date'));
			setEndErrorMessage(__(' '));
			isValid = false;
		}

		if (isValid) {
			detailsRef.current?.removeAttribute('open');
			buildSummaryMessage(start, end);
			onIntervalUpdate({ start, end });

			setStartErrorMessage('');
			setEndErrorMessage('');
			setStartAfterEndError(false);
		}
	};

	const updateBoundaryAt = (boundary) => (date) => {
		if (boundary === 'start') {
			setStart(date);
			if (startAfterEndError) {
				setEndErrorMessage('');
				setStartAfterEndError(false);
			}
			setStartErrorMessage('');
		} else {
			setEnd(date);
			setEndErrorMessage('');
		}
		setRangeOptionValue('');
	};

	// Uses for invalidating dates in the BoundaryInput component
	const byCheckingBoundary = (boundary) => (date) => {
		if ( date > Date.now() ){
			return true;
		}
		if (boundary === 'start') {
			return date > new Date(end);
		}
		return date < new Date(start);
	}

	const onSelectOption = (evt) => {
		const selected = evt.target.value;
		if (selected === '') {
			return;
		}
		setEnd('now');
		setRangeOptionValue(selected);
		setStart(selected);
		setRefresh(true);
		onFilter();
	}

	return (
		<div className="wpcloud-metrics-toolbar">
			<details className="wpcloud-metrics-datetime-picker dropdown" ref={detailsRef}>
				<summary>{summaryText}</summary>
				<ul className="wpcloud-metrics-datetime-picker__ranges"
				>
					<li>
						<div className="wpcloud-metrics-datetime-picker__controls">
							<div className="wpcloud-metrics-datetime-picker__options">
								<p>{__('Quick Range')}</p>
								<select
									value={rangeOptionValue}
									onChange={onSelectOption}
									onClick={() => setOpenedCalRef(null)}
								>
									{Object.entries(rangeOptions).map(([value, label]) => (
										<option key={value} value={value}>{label}</option>
									))}
								</select>
							</div>
							<div className="wpcloud-metrics-datetime-picker__inputs">
								<BoundaryInput
									label={__('Start')}
									value={start}
									errorMessage={startErrorMessage}
									onChange={updateBoundaryAt('start')}
									isInvalidDate={byCheckingBoundary('start')}
									openedCalRef={openedCalRef}
									openingCalendar={setOpenedCalRef}
								/>
								<BoundaryInput
									label={__('End')}
									value={end}
									errorMessage={endErrorMessage}
									onChange={updateBoundaryAt('end')}
									isInvalidDate={byCheckingBoundary('end') }
									openedCalRef={openedCalRef}
									openingCalendar={setOpenedCalRef}
								/>
								<div className="wpcloud-metrics-datetime-picker__refresh">
									<button onClick={() => {
											setRefresh(true);
											onFilter()
										} } >
										{__('filter')}
									</button>
								</div>
							</div>
						</div>
					</li>
				</ul>
			</details>
		</div>
	);
}