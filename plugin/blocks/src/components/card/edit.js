/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';
import { ToggleControl, PanelBody, ToolbarGroup } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
//import './editor.scss';

/**
 *
 * @param {Object} props               Component props.
 * @param {Object} props.attributes
 * @param {Object} props.setAttributes
 * @return {Element} Element to render.
 */
export default function Edit( {
	attributes,
	setAttributes,
	clientId,
	isSelected,
}) {
	const blockProps = useBlockProps();

	const isChildSelected = useSelect( ( select ) =>
		select( 'core/block-editor' ).hasSelectedInnerBlock( clientId, true )
	);

	useEffect(() => {
		setAttributes({ hideContent: ! ( isSelected || isChildSelected ) });
	}, [ isChildSelected, setAttributes, isSelected ]);

	const template = [
		['wpcloud/card-section', { section: "header", metadata: { name: "Header" } }],
		['core/paragraph', {metadata: { name: "Body" }}],
		['wpcloud/card-section', { section: "footer", tag: "span", metadata: { name: "Footer" } }],
	];

	const innerBlocksProps = useInnerBlocksProps(blockProps, {
		template,
	} );

	return (
		<>
			<article
				{ ...blockProps }
				{ ...innerBlocksProps }
			/>
		</>
	);
}
