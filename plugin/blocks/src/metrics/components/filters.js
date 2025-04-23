/**
 * External dependencies
 */
import { useState } from 'react';
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { FlexItem, Flex } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { Icon, close } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import FiltersForm from './filtersForm';

// Helper function to create a compact representation of a filter
const getCompactFilterText = (filter) => {
	const { field, operator, value } = filter;

	// Check if the value contains multiple words
	const hasMultipleWords = value.trim().includes(' ') || value.trim().includes(',');

	return `${field} ${operator} ${hasMultipleWords ? '...' : value}`;
};

// Helper function to create a detailed representation of a filter
const getDetailedFilterText = (filter) => {
	const { field, operator, value } = filter;
	return `${field} ${operator} ${value}`;
};

export default ({ onFiltersChange = () => {} }) => {
	const [filters, setFilters] = useState([]);

	const handleFilterAdd = (filter) => {
		const updatedFilters = [...filters, {
			enabled: true,
			value: filter,
			compact: getCompactFilterText(filter),
			detailed: getDetailedFilterText(filter),
		}];

		setFilters(updatedFilters);
		onFiltersChange(updatedFilters);
	};

	const handleFilterRemove = (indexToRemove) => {
		const updatedFilters = filters.filter((_, index) => index !== indexToRemove);
		setFilters(updatedFilters);
		onFiltersChange(updatedFilters);
	};

	const handleToggleFilter = (indexToDisable) => {
		const updatedFilters = filters.map((filter, index) => {
			if (index === indexToDisable) {
				return { ...filter, enabled: !filter.enabled };
			}
			return filter;
		});
		setFilters(updatedFilters);
		onFiltersChange(updatedFilters);
	}

	return (
		<FlexItem isBlock={true}>
			<div className="wpcloud-metrics-filters">
				{filters.length > 0 && (
					<Flex className="wpcloud-metrics-filters__applied" justify="start">
						{filters.map((filter, index) => (
							<button
								key={index}
								className={
									classnames(
										"wpcloud-metrics-filters__filter secondary",
										{ "disabled": !filter.enabled }
									)}
								{ ...( filter.compact != filter.detailed && { 'data-tooltip': filter.detailed } ) }
								onClick={() => handleToggleFilter(index)}
							>
								<span className="wpcloud-metrics-filters__filter-text">
									{getCompactFilterText(filter.value)}
								</span>
								<span
									className="wpcloud-metrics-filters__filter-remove"
									onClick={() => handleFilterRemove(index)}
									aria-label={__('Remove filter')}
								>
									<Icon icon={close} size={12} />
								</span>
							</button>
						))}
					</Flex>
				)}

				<FiltersForm onFilterAdd={handleFilterAdd} />
			</div>
		</FlexItem>
	);
};
