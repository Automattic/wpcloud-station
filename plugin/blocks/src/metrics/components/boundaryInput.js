import { useState, useId, useRef, useEffect } from 'react';

import { DatePicker } from '@wordpress/components';
import * as icons from '@wordpress/icons';
const Icon = icons.Icon;

const identity = (v) => v;

export default function ({ label, isInvalid, errorMessage, value, openedCalRef, onChange = identity, isInvalidDate = identity, openingCalendar = identity,  }) {
	const [showDatePicker, setShowDatePicker] = useState(false);
	const id = useId();
	const calRef = useRef(null);

	useEffect(() => {
		if ( openedCalRef?.current !== calRef?.current ) {
			setShowDatePicker(false);
		}
	}, [openedCalRef, calRef]);

	const input = () => {
		if (! isInvalid) {
			return (
				<input
					type="text"
					value={value}
					onChange={evt => onChange(evt.target.value)} />
			);
		}

		return (
			<>
				<input
					type="text"
					value={value}
					id={id}
					aria-invalid={true}
					onChange={evt => onChange(evt.target.value)} />
				{errorMessage && <small id={id} >{errorMessage}</small>}
			</>
		)
	}

	return (
		<div className="wpcloud-metrics-input">
			<label>
				{label}
				{input()}
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
								isInvalidDate={isInvalidDate}
						/>
					</div>
				)}
		</div>
	);
}