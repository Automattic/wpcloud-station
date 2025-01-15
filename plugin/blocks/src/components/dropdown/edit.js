/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useCallback } from '@wordpress/element';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
/**
 * Internal dependencies
 */
import { updateAttribute } from '../controls/utils';
import './editor.scss';

export default function Edit({ attributes, setAttributes }) {

	const blockProps = useBlockProps();

	const { hideChevron, } = attributes;

	const update = updateAttribute(setAttributes);

	const template = [
		['wpcloud/dropdown-summary'],
		['wpcloud/dropdown-list'],
	];
	const innerBlocksProps  = useInnerBlocksProps(blockProps, {
		template, allowedBlocks: [ 'wpcloud/dropdown-summary', 'wpcloud/dropdown-list' ]
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Dropdown Settings')}>
					<ToggleControl
						label={__('Hide Chevron')}
						checked={hideChevron}
						onChange={update('hideChevron')}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...innerBlocksProps}
				className={classnames(blockProps.className, 'dropdown')}
			/>
		</>
	);
}
