/**
 * External dependencies
 */
import { useState } from 'react';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
    Button,
    Flex,
    FlexItem,
    Panel,
    PanelBody,
    PanelRow,
    SelectControl,
    TextControl,
    Icon
} from '@wordpress/components';
import { trash } from '@wordpress/icons';

/**
 * Internal dependencies
 */

// Field options (dimensions) - same as in filtersForm.js
const fieldOptions = {
    'http_verb': __('HTTP Verb'),
    'http_status': __('HTTP Status')
};

// Operator options - same as in filtersForm.js
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

/**
 * Convert a filter from the array format [field, operator, value] to an object format
 *
 * @param {Array} filter - Filter in array format [field, operator, value]
 * @returns {Object} - Filter in object format {field, operator, value}
 */
const arrayToObjectFilter = (filter) => {
    if (Array.isArray(filter)) {
        return {
            field: filter[0],
            operator: filter[1],
            value: filter[2]
        };
    }
    return filter;
};

/**
 * Convert a filter from the object format {field, operator, value} to an array format
 *
 * @param {Object} filter - Filter in object format {field, operator, value}
 * @returns {Array} - Filter in array format [field, operator, value]
 */
const objectToArrayFilter = (filter) => {
    if (filter && typeof filter === 'object' && !Array.isArray(filter)) {
        return [filter.field, filter.operator, filter.value];
    }
    return filter;
};

/**
 * Component for building and managing predefined filters in the block editor
 */
export default function FilterBuilder({ filters = [], onChange }) {
    const [field, setField] = useState('');
    const [operator, setOperator] = useState('');
    const [value, setValue] = useState('');

    const handleFieldChange = (newField) => {
        setField(newField);
    };

    const handleOperatorChange = (newOperator) => {
        setOperator(newOperator);
    };

    const handleValueChange = (newValue) => {
        setValue(newValue);
    };

    const resetForm = () => {
        setField('');
        setOperator('');
        setValue('');
    };

    const handleAddFilter = () => {
        if (field && operator && value) {
            // Convert value to number if it's numeric and using a numeric comparison operator
            let processedValue = value;
            if (['>','>=','<','<='].includes(operator) && !isNaN(Number(value))) {
                processedValue = Number(value);
            } else if (field === 'http_status' && !isNaN(Number(value))) {
                // HTTP status codes should be numbers
                processedValue = Number(value);
            }

            // Create the new filter in array format [field, operator, value]
            const newFilter = [field, operator, processedValue];

            // Add the new filter to the existing filters
            const updatedFilters = [...filters, newFilter];

            // Call the onChange callback with the updated filters
            onChange(updatedFilters);

            // Reset the form
            resetForm();
        }
    };

    const handleRemoveFilter = (indexToRemove) => {
        const updatedFilters = filters.filter((_, index) => index !== indexToRemove);
        onChange(updatedFilters);
    };

    return (
        <div className="wpcloud-filter-builder">
            <PanelBody title={__('Graph Filters')} initialOpen={false}>
                <PanelRow>
                    <div style={{ width: '100%' }}>
                        <Flex direction="column" gap={4}>
                            {/* Filter input form */}
                            <Flex direction="column" gap={2}>
                                <SelectControl
                                    label={__('Field')}
                                    value={field}
                                    options={[
                                        { label: __('Select a field'), value: '' },
                                        ...Object.entries(fieldOptions).map(([value, label]) => ({
                                            label,
                                            value
                                        }))
                                    ]}
                                    onChange={handleFieldChange}
                                />

                                <SelectControl
                                    label={__('Operator')}
                                    value={operator}
                                    options={[
                                        { label: __('Select an operator'), value: '' },
                                        ...Object.entries(operatorOptions).map(([value, label]) => ({
                                            label,
                                            value,
                                            disabled: field && !isOperatorValidForField(field, value)
                                        }))
                                    ]}
                                    onChange={handleOperatorChange}
                                    disabled={!field}
                                />

                                <TextControl
                                    label={__('Value')}
                                    value={value}
                                    onChange={handleValueChange}
                                    placeholder={
                                        operatorAcceptsMultipleValues(operator)
                                            ? __('Value one, Value two, ...')
                                            : __('Value')
                                    }
                                    disabled={!field || !operator}
                                />

                                <Button
                                    variant="primary"
                                    onClick={handleAddFilter}
                                    disabled={!field || !operator || !value}
                                >
                                    {__('Add Filter')}
                                </Button>
                            </Flex>

                            {/* List of existing filters */}
                            {filters.length > 0 && (
                                <div className="wpcloud-filter-builder__filters">
                                    <h3>{__('Current Filters')}</h3>
                                    <Flex direction="column" gap={2}>
                                        {filters.map((filter, index) => {
                                            // Convert to object format for easier access
                                            const f = arrayToObjectFilter(filter);
                                            return (
                                                <Flex key={index} align="center" justify="space-between">
                                                    <span>
                                                        {`${f.field} ${f.operator} ${f.value}`}
                                                    </span>
                                                    <Button
                                                        isDestructive
                                                        icon={trash}
                                                        onClick={() => handleRemoveFilter(index)}
                                                        label={__('Remove filter')}
                                                        showTooltip
                                                    />
                                                </Flex>
                                            );
                                        })}
                                    </Flex>
                                </div>
                            )}
                        </Flex>
                    </div>
                </PanelRow>
            </PanelBody>
        </div>
    );
}
