import { useState, useRef, useEffect, useCallback, useId} from 'react';
import { __, _n, sprintf } from '@wordpress/i18n';


import BoundaryInput from './boundaryInput';
const defaultRangeOptions = {
	'now-1h': __('Last 1 Hour'), // default
	'now-5m': __('Last 5 Minutes'),
	'now-15m': __('Last 15 Minutes'),
	'now-30m': __('Last 30 Minutes'),
	'now-3h': __('Last 3 Hours'),
	'now-6h': __('Last 6 Hours'),
	'now-12h': __('Last 12 Hours'),
	'now-2d': __('Last 2 Days'),
	'now-7d': __('Last 7 Days'),
	'now-30d': __('Last 30 Days'),
	'now-90d': __('Last 90 Days'),
	'now-6M': __('Last 6 Months'),
	'now-1y': __('Last Year'),
	'now-2y': __('Last 2 Years'),
}

const units = {
	's': __('second'),
	'm': __('minute'),
	'h': __('hour'),
	'd': __('day'),
	'w': __('week'),
	'M': __('month'),
	'y': __('year'),
}

function getFromNow(date) {
	const [match, now, dash, amount, unit] = date.match(/(now)(.?)(?:(\d+)([smhdMy]+))?/) || [];

	if (!match) {
		return [];
	}
	// make sure the dash is a dash
	if (dash && dash !== '-') {

		return [];
	}
	// 'now-' is not valid
	if (dash && !amount) {
		return [];
	}
	// 'now' is valid
	if (!amount) {
		return [now];
	}
	// 'now-1' is not valid
	if (amount && !unit) {
		return [];
	}
	// 'now-1s' is valid
	return [now, amount, unit];
}

function isValidDate(date) {
	// try parsing the date
	const parsed = Date.parse(date);
	if (!isNaN(parsed)) {
		return true;
	}
	const nowDate = getFromNow(date);
	if (nowDate.length) {
		return true;
	}
	return false;
}

function parseRelativeTime(input) {
	const [isNow, amount, unit] = getFromNow(input);
	if (!isNow) {
		return null
	}
	let now = new Date();

	switch (unit) {
		case "s": now.setSeconds(now.getSeconds() - amount); break;
		case "m": now.setMinutes(now.getMinutes() - amount); break;
		case "h": now.setHours(now.getHours() - amount); break;
		case "d": now.setDate(now.getDate() - amount); break;
		case "w": now.setDate(now.getDate() - amount * 7); break;
		case "M": now.setMonth(now.getMonth() - amount); break;
		case "y": now.setFullYear(now.getFullYear() - amount); break;
	}

	return now.toISOString().replace("T", " ").split(".")[0];
}

export default ({ rangeOptions, interval, onIntervalUpdate = () => { } }) => {

	const detailsRef = useRef(null);

	const [start, setStart] = useState( interval.start || 'now-1h' );
	const [end, setEnd] = useState( interval.end || 'now' );
	const [isStartInvalid, setIsStartInvalid] = useState(false);
	const [isEndInvalid, setIsEndInvalid] = useState(false);

	const [summaryText, setSummaryText] = useState('Last 1 Hour');

	// @TODO: figure out how to reset this when using the inputs
	const [rangeOptionValue, setRangeOptionValue] = useState('now-1h');

	const[ refresh, setRefresh ] = useState(false);
	const [error, setError] = useState([]);

	let from, to;

	const buildSummaryMessage = (start, end) => {
		if (!isValidDate(start) || !isValidDate(end)) {
			return;
		}
		let newSummary = summaryText;
		const [endNow, endAmount, endUnit] = getFromNow(end);
		const [startNow, startAmount, startUnit] = getFromNow(start);

		if (endNow && startNow) {
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

			// @TODO: handle the case where the end is using now-*
		} else {
			from = start.replace('T', ' ');
			to = end.replace('T', ' ');
			newSummary = `${from} → ${to}`;
		}
		setSummaryText(newSummary);
	}

	useEffect(() => {
		buildSummaryMessage(start, end);
	}, []);

	// Add a global keydown listener to trigger the refresh function when the user presses Enter
	useEffect(() => {
		const handleKeyDown = (event) => {
			if (event.key === 'Enter') {
				const detailsElement = detailsRef.current;
				if (detailsElement && detailsElement.hasAttribute('open')) {
					setRefresh(true);
					onRefresh();
				}
			}
		};

		document.addEventListener('keydown', handleKeyDown);

		return () => {
			document.removeEventListener('keydown', handleKeyDown);
		};
	 }, [start, end]);

	const onRefresh = () => {
		const errors = [];
		setError([]);
		const parsedStart = Date.parse(start);
		const parsedEnd = Date.parse(end);

		const [endNow, endAmount, endUnit] = getFromNow(end);
		const [startNow, startAmount, startUnit] = getFromNow(start);

		if (isNaN(parsedStart)) {
			if (!startAmount) {
				errors.push(__('Invalid start date'));
				setIsStartInvalid(true);
			}
		}

		if (isNaN(parsedEnd)) {
			if (!endNow) {
				errors.push(__('Invalid end date'));
				setIsEndInvalid(true);
			}

			//make sure the end amount/unit is less than the start amount/unit
			const unitOrder = Object.keys(units);
			if (unitOrder.indexOf(endUnit) > unitOrder.indexOf(startUnit)) {
				errors.push(__('End date must be before start date'));
			}
			if (endUnit === startUnit && endAmount > startAmount) {
				errors.push(__('End date must be before start date'));
			}
		}

		if ( !isNaN(parsedStart) && !isNaN(parsedEnd) && parsedStart > parsedEnd) {
			errors.push(__('Start date must be before end date'));
		}

		// @TODO: validate when one is now-... and the the other is a date

		if (errors.length) {
			setError(errors);
			return;
		}

		detailsRef.current?.removeAttribute('open');
		buildSummaryMessage(start, end);
		onIntervalUpdate({ start, end })
	};

	useEffect(() => {
		if (refresh) {
			onRefresh();
			setRefresh(false);
		}
	}, [refresh, start, end]);

	const onSelectOption = useCallback((evt) => {
		setEnd('now');
		setRangeOptionValue(evt.target.value);
		setStart(evt.target.value);
		setRefresh(true);
		onRefresh();
	}, [start, end]);

	const showErrors = () => {
		if (!error.length) {
			return null;
		}
		return (
			<li>
				<article className="wpcloud-metrics-toolbar__errors">
					<ul>
					{error.map((msg, index) => (
						<li key={index} className="error">{msg}</li>
					))}
					</ul>
				</article>
			</li>
		);
	}

	rangeOptions = { ...defaultRangeOptions, ...rangeOptions };
	return (
		<div className="wpcloud-metrics-toolbar">
			<details className="wpcloud-metrics-datetime-picker dropdown" ref={detailsRef}>
				<summary>{summaryText}</summary>
				<ul className="wpcloud-metrics-datetime-picker__ranges"
				>
					{showErrors()}
					<li>
						<div className="wpcloud-metrics-datetime-picker__controls">
							<div className="wpcloud-metrics-datetime-picker__options">
								<p>{__('Quick Range')}</p>
								<select value={ rangeOptionValue } onChange={onSelectOption}>
									{Object.entries(rangeOptions).map(([value, label]) => (
										<option key={value} value={value}>{label}</option>
									))}
								</select>
							</div>
							<div className="wpcloud-metrics-datetime-picker__inputs">
								<BoundaryInput
									label={__('Start')}
									value={start}
									isInvalid={isStartInvalid}
									errorMessage={__('Invalid start date')}
									onChange={setStart}
									isInvalidDate={date => date > end || date > Date.now()}
								/>
								<BoundaryInput
									label={__('End')}
									value={end}
									isInvalid={isEndInvalid}
									errorMessage={__('Invalid end date')}
									onChange={setEnd}
									isInvalidDate={date => date < start || date > Date.now()} />
								<div className="wpcloud-metrics-datetime-picker__refresh">
									<button onClick={() => {
											setRefresh(true);
											onRefresh()
										} } >
										{__('Refresh')}
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