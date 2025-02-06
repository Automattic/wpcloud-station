import { useState, useId } from 'react';

import { DatePicker } from '@wordpress/components';
import * as icons from '@wordpress/icons';
const Icon = icons.Icon;

export default function ({ label, isInvalid, onChange, errorMessage, value, isInvalidDate }) {
	const [showDatePicker, setShowDatePicker] = useState(false);
	const id = useId();

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
				<Icon icon={icons.calendar} size={30} onClick={() => setShowDatePicker(!showDatePicker)} />
			</label>

				{showDatePicker && (
					<div className="wpcloud-datepicker">
						<DatePicker
							onChange={(date) => {
								onChange(date);
								setShowDatePicker(false);
							}}
								isInvalidDate={isInvalidDate}
						/>
					</div>
				)}
		</div>
	);
}