/**
 * External dependencies
 */
import { useState, useEffect } from 'react';
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { FlexItem, Flex } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { Icon, trash } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import FiltersForm from './filtersForm';


const buildFilter = (filter, enabled = true) => {
	const [ field, operator, value ] = Array.isArray(filter) ? filter : [filter.field, filter.operator, filter.value];
	const hasMultipleWords = value.trim().includes(' ') || value.trim().includes(',');
	const label = `${field} ${operator} ${value}`;

	return {
		enabled,
		value: {
			field,
			operator,
			value,
		},
		compact: hasMultipleWords ? `${field} ${operator} ...` : label,
		label,
	}
}

const updateFilters = (onFilterUpdate, setFilters) => {
	return (filters => {
		setFilters(filters);
		const filterList = filters
			.filter(f => f.enabled)
			.map(f => [f.value.field, f.value.operator, f.value.value]);
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
