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
	className,
	clientId,
	isSelected,
} ) {
	const { adminOnly, button, open, outline, contrast, secondary } = attributes;
	const blockProps = useBlockProps();

	const isChildSelected = useSelect( ( select ) =>
		select( 'core/block-editor' ).hasSelectedInnerBlock( clientId, true )
	);

	useEffect(() => {
		setAttributes({ hideContent: ! ( isSelected || isChildSelected ) });
	}, [ isChildSelected, setAttributes, isSelected ]);

	const updateAttribute = ( key ) => ( val ) => {
		setAttributes( { [ key ]: val } );
	};
	const controls = (
		<InspectorControls>
			<PanelBody title={ __( 'Form Settings' ) }>
				<ToggleControl
					label={ __( 'Open by default' ) }
					checked={ open }
					onChange={ updateAttribute( 'open' ) }
				/>
				<ToggleControl
					label={ __( 'Admin Only' ) }
					checked={ adminOnly }
					 onChange={ updateAttribute( 'adminOnly' ) }
				/>
				<ToggleControl
					label={ __( 'Display as Button' ) }
					checked={ button }
					onChange={ updateAttribute( 'button' ) }
				/>
				{ button && (
					<>
						<ToggleControl
							label={ __( 'outline' ) }
							checked={ outline }
							onChange={ updateAttribute( 'outline' ) }
						/>
						<ToggleControl
							label={ __( 'contrast' ) }
							checked={ contrast }
							onChange={ updateAttribute( 'contrast' ) }
						/>
						<ToggleControl
							label={ __( 'secondary' ) }
							checked={ secondary }
							onChange={ updateAttribute( 'secondary' ) }
						/>
					</>
					)}
			</PanelBody>
		</InspectorControls>
	);

	const template = [
		[ 'core/group', { metadata: { name: 'Heading' } }, [ [ 'core/heading', { level: 3 } ] ] ],
		[ 'core/group', { metadata: { name: 'Content' } }, [ [ 'core/paragraph' ] ] ],
	];

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template,
	} );

	return (
		<>
			{ controls }
			<div
				{ ...innerBlocksProps }
			/>
		</>
	);
}
