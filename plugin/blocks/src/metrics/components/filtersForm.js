/**
 * External dependencies
 */
import { useState } from 'react';

/**
 * WordPress dependencies
 */
import { FlexItem } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */

// Field options (dimensions)
const fieldOptions = {
		'http_verb': __('HTTP Verb'),
		'http_status': __('HTTP Status')
};

// Operator options
const operatorOptions = {
		'=': __('is'),
		'!=': __('is not'),
		'IN': __('is one of'),
		'NOT IN': __('is not one of'),
		'>': __('greater than'),
		'>=': __('greater than or equal to'),
		'<': __('less than'),
		'<=': __('less than or equal to'),
};

export default ({ onFilterAdd = () => {} }) => {
		const [field, setField] = useState('http_verb');
		const [operator, setOperator] = useState('IN');
		const [isOpen, setIsOpen] = useState(false);

		const handleFieldChange = (event) => {
				setField(event.target.value);
		};

		const handleOperatorChange = (event) => {
				setOperator(event.target.value);
		};

		return (
				<FlexItem isBlock={true}>
						<details
								className="wpcloud-metrics-filters-picker dropdown"
								open={isOpen}
								onToggle={(e) => setIsOpen(e.target.open)}
						>
								<summary>{__('Add Filter')}</summary>
								<ul className="wpcloud-metrics-filters-picker__options">
										<li>
												<div className="wpcloud-metrics-filters-picker__controls">
														<div className="wpcloud-metrics-filters-picker__inputs">
																<div className="wpcloud-metrics-filters-picker__field">
																		<label htmlFor="filter-field">{__('Field')}</label>
																		<select
																				id="filter-field"
																				value={field}
																				onChange={handleFieldChange}
																		>
																				{Object.entries(fieldOptions).map(([value, label]) => (
																						<option key={value} value={value}>{label}</option>
																				))}
																		</select>
																</div>
																<div className="wpcloud-metrics-filters-picker__operator">
																		<label htmlFor="filter-operator">{__('Operator')}</label>
																		<select
																				id="filter-operator"
																				value={operator}
																				onChange={handleOperatorChange}
																		>
																				{Object.entries(operatorOptions).map(([value, label]) => (
																						<option key={value} value={value}>{label}</option>
																				))}
																		</select>
																</div>
																<div className="wpcloud-metrics-filters-picker__apply">
																		<button onClick={onFilterAdd}>
																				{__('Apply')}
																		</button>
																</div>
														</div>
												</div>
										</li>
								</ul>
						</details>
				</FlexItem>
		);
};
