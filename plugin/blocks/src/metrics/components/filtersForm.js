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

// Validator function to check if the field allows the operator
const isOperatorValidForField = (field, operator) => {
		// For now, always return true
		// This can be expanded later to validate specific operators for different fields
		return true;
};

// Check if operator accepts multiple values
const operatorAcceptsMultipleValues = (operator) => {
		return operator === 'IN' || operator === 'NOT IN';
};

export default ({ onFilterAdd = () => {} }) => {
		const [field, setField] = useState('');
		const [operator, setOperator] = useState('');
		const [value, setValue] = useState('');
		const [isOpen, setIsOpen] = useState(false);

		const handleFieldChange = (event) => {
				setField(event.target.value);
		};

		const handleOperatorChange = (event) => {
				setOperator(event.target.value);
		};

		const handleValueChange = (event) => {
				setValue(event.target.value);
		};

		const resetForm = () => {
				setField('');
				setOperator('');
				setValue('');
		};

		const handleAddFilter = () => {
				if (field && operator && value) {
						onFilterAdd({ field, operator, value });
						resetForm();
						setIsOpen(false); // Close the dropdown after adding a filter
				}
		};

		const handleKeyDown = (event) => {
				if (event.key === 'Enter' && field && operator && value) {
						event.preventDefault();
						handleAddFilter();
				}
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
																		<label htmlFor="filter-field" className="screen-reader-text">{__('Field')}</label>
																		<select
																				id="filter-field"
																				value={field}
																				onChange={handleFieldChange}
																				aria-label={__('Field')}
																		>
																				<option value="">{__('Field')}</option>
																				{Object.entries(fieldOptions).map(([value, label]) => (
																						<option key={value} value={value}>{label}</option>
																				))}
																		</select>
																</div>
																<div className="wpcloud-metrics-filters-picker__operator">
																		<label htmlFor="filter-operator" className="screen-reader-text">{__('Operator')}</label>
																		<select
																				id="filter-operator"
																				value={operator}
																				onChange={handleOperatorChange}
																				disabled={!field}
																				aria-label={__('Operator')}
																		>
																				<option value="">{__('Operator')}</option>
																				{Object.entries(operatorOptions).map(([value, label]) => (
																						<option
																								key={value}
																								value={value}
																								disabled={field && !isOperatorValidForField(field, value)}
																						>
																								{label}
																						</option>
																				))}
																		</select>
																</div>
																<div className="wpcloud-metrics-filters-picker__value">
																		<label htmlFor="filter-value" className="screen-reader-text">{__('Value')}</label>
																		<input
																				type="text"
																				id="filter-value"
																				value={value}
																				onChange={handleValueChange}
																				onKeyDown={handleKeyDown}
																				disabled={!field || !operator}
																				placeholder={operatorAcceptsMultipleValues(operator) ? __('Value one, Value two, ...') : __('Value')}
																				aria-label={__('Value')}
																		/>
																</div>
																<div className="wpcloud-metrics-filters-picker__apply">
																		<button
																				onClick={handleAddFilter}
																				disabled={!field || !operator || !value}
																		>
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
