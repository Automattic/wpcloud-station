/**
 * External dependencies
 */
import { useState, useEffect } from 'react';
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { Flex, FlexItem } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { Icon, trash } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import FiltersForm from './filtersForm';


const buildFilter = (filter, enabled = true) => {
	// Handle different filter formats
	let field, operator, value;

	if (Array.isArray(filter)) {
		// Array format: [field, operator, value]
		[field, operator, value] = filter;
	} else if (filter && typeof filter === 'object') {
		// Object format: {field, operator, value} or {value: {field, operator, value}}
		if (filter.value && filter.value.field) {
			// Format: {value: {field, operator, value}}
			field = filter.value.field;
			operator = filter.value.operator;
			value = filter.value.value;
		} else {
			// Format: {field, operator, value}
			field = filter.field;
			operator = filter.operator;
			value = filter.value;
		}
	} else {
		// Invalid format, provide defaults
		field = '';
		operator = '=';
		value = '';
	}

	// Ensure all values are defined
	field = field || '';
	operator = operator || '=';
	value = value !== undefined ? value : '';

	// Convert value to string for display
	const valueStr = String(value);
	const hasMultipleWords = valueStr.trim().includes(' ') || valueStr.trim().includes(',');
	const label = `${field} ${operator} ${valueStr}`;

	return {
		enabled,
		value: {
			field,
			operator,
			// Store the original value to maintain type for API calls
			value,
		},
		// Use the string version for display
		compact: hasMultipleWords ? `${field} ${operator} ...` : label,
		label,
	}
}

const updateFilters = (onFilterUpdate, setFilters) => {
	return (filters => {
		setFilters(filters);
		// Extract the field, operator, and value from each filter and create an array format
		// that the API expects: [field, operator, value]
		const filterList = filters
			.filter(f => f.enabled)
			.map(f => {
				// Ensure the filter has the expected structure
				if (f.value && f.value.field && f.value.operator && f.value.value !== undefined) {
					// Convert value to string to avoid PHP strpos() errors
					return [f.value.field, f.value.operator, String(f.value.value)];
				}
				// Fallback for unexpected filter format
				return null;
			})
			.filter(f => f !== null); // Remove any null entries

		onFilterUpdate({ filters: filterList });
	});
}

export default ({ onFiltersUpdate = console.log, filters: propFilters = [], loading = false }) => {
	const [filters, setFilters] = useState([]);
	const [isOpen, setIsOpen] = useState(false);

	useEffect(() => {
		if (propFilters.length > 0) {
			const updatedFilters = propFilters.map(filter => buildFilter(filter));
			setFilters(updatedFilters);
		}
	}, [propFilters]);


	const handleFilterUpdate =  updateFilters(onFiltersUpdate, setFilters);

	const handleFilterAdd = (filter) => {
		const updatedFilters = [...filters, buildFilter(filter)];
		handleFilterUpdate(updatedFilters);
	};

	const handleFilterRemove = (indexToRemove) => {
		const updatedFilters = filters.filter((_, index) => index !== indexToRemove);
		handleFilterUpdate(updatedFilters);
	};

	const handleToggleFilter = (indexToDisable) => {
		const updatedFilters = filters.map((filter, index) => {
			if (index === indexToDisable) {
				return { ...filter, enabled: !filter.enabled };
			}
			return filter;
		});
		handleFilterUpdate(updatedFilters);
	}
	const toggleOpen = () => {
		if (!loading) {
			setIsOpen(!isOpen);
		}
	};

	return (
		<div className="wpcloud-metrics-filters__container" style={{ position: 'relative' }}>
			<summary
				onClick={toggleOpen}
				style={{
					cursor: loading ? 'not-allowed' : 'pointer',
					opacity: loading ? 0.6 : 1
				}}
			>
				{filters.length > 0 ? __(`Filters (${filters.filter(f => f.enabled).length})`) : __('Filters')}
			</summary>
			<div
				className="wpcloud-metrics-filters"
				style={{
					display: isOpen ? 'block' : 'none',
					position: 'absolute',
					zIndex: 1000,
					background: 'white',
					boxShadow: '0 2px 5px rgba(0,0,0,0.2)',
					padding: '10px',
					borderRadius: '4px',
					width: '100%'
				}}
			>
				<Flex className="wpcloud-metrics-filters__applied" justify="start" wrap="wrap" style={{ gap: '8px' }}>
					{filters.map((filter, index) => (
						<button
							key={index}
							className={
								classnames(
									"wpcloud-metrics-filters__filter secondary",
									{ "disabled": !filter.enabled }
								)}
							{ ...( filter.compact != filter.detailed && { 'data-tooltip': filter.label } ) }
							onClick={() => handleToggleFilter(index)}
						>
							<span className="wpcloud-metrics-filters__filter-text">
								{filter.compact}
							</span>
							<Icon
								onClick={(e) => {
									e.stopPropagation(); // Stop event from bubbling up to parent button
									handleFilterRemove(index);
								}}
								icon={trash} size={20} />
						</button>
					))}
					<FiltersForm onFilterAdd={handleFilterAdd} />
				</Flex>
			</div>
		</div>
	)
};
