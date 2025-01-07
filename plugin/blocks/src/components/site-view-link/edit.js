/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	RichText,
	useBlockProps,
	InnerBlocks,
} from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	TextControl,
} from '@wordpress/components';

/**
 *
 * Internal dependencies
 */
//import './editor.scss';

function ButtonBlock( { attributes, setAttributes } ) {
	const { adminOnly, view} =
		attributes;
	const blockProps = useBlockProps();


	const controls = (
		<>
			<InspectorControls>
				<PanelBody label={ __( 'Settings' ) }>
					<TextControl
						label={ __( 'View' ) }
						value={ view }
						onChange={(newView) => {
							setAttributes({
								view: newView,
							});
						}} />
					<ToggleControl
						label={ __( 'Limit to Admins' ) }
						checked={ adminOnly }
						onChange={ ( newVal ) => {
							setAttributes( {
								adminOnly: newVal,
							} );
						} }
						help={ __(
							'Only admins will see this field. Inputs marked as admin only will appear with a dashed border in the editor'
						) }
					/>
				</PanelBody>
			</InspectorControls>
		</>
	);

	return (
		<>
			{ controls }
			<div
				{ ...blockProps }
				className={ classNames(
					blockProps.className,
					{
						'is-admin-only': adminOnly,
					}
				) }
			>

			<InnerBlocks allowedBlocks={ [ 'core/heading' ] } />

			</div>
		</>
	);
}

export default ButtonBlock;
