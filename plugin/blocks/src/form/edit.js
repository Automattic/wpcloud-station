/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	InnerBlocks,
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { ToggleControl, TextControl, PanelBody } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import './editor.scss';

/**
 *
 * @param {Object}  props               Component props.
 * @param {Object}  props.attributes
 * @param {Object}  props.setAttributes
 * @param {number}  props.clientId
 * @param {boolean} props.isSelected
 * @return {Element} Element to render.
 */
export default function Edit( {
	attributes,
	setAttributes,
	clientId,
	isSelected,
} ) {
	const { action, ajax, wpcloudAction, inline } = attributes;
	const blockProps = useBlockProps();

	const isChildSelected = useSelect( ( select ) =>
		select( 'core/block-editor' ).hasSelectedInnerBlock( clientId )
	);

	const { hasInnerBlocks } = useSelect(
		( select ) => {
			const { getBlock } = select( blockEditorStore );
			const block = getBlock( clientId );
			return {
				hasInnerBlocks: !! ( block && block.innerBlocks.length ),
			};
		},
		[ clientId ]
	);

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		renderAppender:
			! hasInnerBlocks || isSelected || isChildSelected
				? InnerBlocks.ButtonBlockAppender
				: undefined,
		template: hasInnerBlocks ? undefined : [ [ 'wpcloud/form-input' ] ],
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Form Settings' ) }>
					<TextControl
						label={ __( 'WP Cloud Action' ) }
						value={ wpcloudAction }
						onChange={ ( newValue ) =>
							setAttributes( { wpcloudAction: newValue } )
						}
						help={ __( 'The WP Cloud form action.' ) }
					/>
					<ToggleControl
						label={ __( 'Display Inline' ) }
						checked={ inline }
						onChange={ ( newVal ) => {
							setAttributes( {
								inline: newVal,
							} );
						} }
					/>

					<ToggleControl
						label={ __( 'Enable AJAX' ) }
						checked={ attributes.ajax }
						onChange={ ( newValue ) =>
							setAttributes( { ajax: newValue } )
						}
						help={ __(
							'Enable AJAX form submission for a smoother experience.'
						) }
					/>
					{ ! ajax && (
						<TextControl
							label={ __( 'Action' ) }
							value={ action }
							onChange={ ( newValue ) =>
								setAttributes( { action: newValue } )
							}
							help={ __( 'The URL to send the form data to.' ) }
						/>
					) }
				</PanelBody>
			</InspectorControls>
			<form
				{ ...innerBlocksProps }
				className={ classNames(
					innerBlocksProps.className,
					'wpcloud-block-form',
					{
						'is-inline': inline,
					}
				) }
				encType="text/plain"
			/>
		</>
	);
}
