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
import { ToggleControl, PanelBody } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import './editor.scss';

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
	className,
	clientId,
	isSelected,
} ) {
	const { clickToToggle, hideHeader, openOnLoad } = attributes;
	const blockProps = useBlockProps();

	const isChildSelected = useSelect( ( select ) =>
		select( 'core/block-editor' ).hasSelectedInnerBlock( clientId, true )
	);

	useEffect(() => {
		setAttributes({ hideContent: ! ( isSelected || isChildSelected ) });
	}, [ isChildSelected, setAttributes, isSelected ]);

	const controls = (
		<InspectorControls>
			<PanelBody title={ __( 'Form Settings' ) }>
				<ToggleControl
					label={ __( 'Click to Toggle' ) }
					checked={ clickToToggle }
					onChange={ ( newVal ) => {
						setAttributes( {
							clickToToggle: newVal,
						} );
					}}
					help={ __('If enabled clicking anywhere on the section will toggle the expandable content. If disabled use a wpcloud button with the action `wpcloud_toggle_expanding_section` to toggle the content.')}
				/>
				<ToggleControl
					label={__('Hide header when expanded')}
					checked={ hideHeader }
					onChange={ ( newVal ) => {
						setAttributes( {
							hideHeader: newVal,
						} );
					}}
				/>
				<ToggleControl
					label={__('Open by default')}
					checked={ openOnLoad }
					onChange={ ( newVal ) => {
						setAttributes( {
							openOnLoad: newVal,
						} );
					}}
				/>
			</PanelBody>
		</InspectorControls>
	);

	const template = [
		[ 'wpcloud/expanding-header' ],
		[ 'wpcloud/expanding-content' ],
	];

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template,
	} );

	return (
		<>
			{ controls }
			<div
				{ ...innerBlocksProps }
				className={ classNames(
					className,
					'wpcloud-block-expanding-section',
					innerBlocksProps.className,
				) }
			/>
		</>
	);
}
