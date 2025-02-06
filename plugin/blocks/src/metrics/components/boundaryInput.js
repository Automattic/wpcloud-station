import { useState, useId, useRef, useEffect } from 'react';

import { DatePicker } from '@wordpress/components';
import * as icons from '@wordpress/icons';
const Icon = icons.Icon;


import { parseRelativeTime } from '../utils';

const identity = (v) => v;

export default function ({ label, errorMessage, value, openedCalRef, onChange = identity, isInvalidDate = identity, openingCalendar = identity,  }) {
	const [showDatePicker, setShowDatePicker] = useState(false);
	const id = useId();
	const calRef = useRef(null);

	useEffect(() => {
		if ( openedCalRef?.current !== calRef?.current ) {
			setShowDatePicker(false);
		}
	}, [openedCalRef, calRef]);

	const isValid = !errorMessage.length;
	const ariaInvalid = isValid ? {} : { 'aria-invalid': true };
	const calValue = parseRelativeTime(value) || value || new Date();

	return (
		<div className="wpcloud-metrics-input">
			<label>
				{label}
				<input
					type="text"
					value={value}
					id={id}
					onChange={evt => onChange(evt.target.value)}
					{...ariaInvalid}
				/>
				{!isValid && <small id={id}>{errorMessage}</small>}
				<Icon icon={icons.calendar} size={30} onClick={() => {
					setShowDatePicker(!showDatePicker);
					openingCalendar(calRef);
				}
				} />
			</label>
				{showDatePicker && (
					<div className="wpcloud-datepicker" ref={calRef}>
						<DatePicker
							onChange={(date) => {
								onChange(date.replace('T', ' '));
								setShowDatePicker(false);
							}}
							currentDate={calValue}
							isInvalidDate={isInvalidDate}
						/>
					</div>
				)}
		</div>
	);
}