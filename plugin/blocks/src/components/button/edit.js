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
	RichText,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';

import './editor.scss';

const Edit = ( { attributes, setAttributes } ) => {
	const { text, icon, type, displayButton } = attributes;

	const controls = (
		<>
			<InspectorControls>
				<PanelBody title={__('Settings')}>
					<ToggleControl
						label={__('Display as Button')}
						checked={displayButton}
						onChange={(newVal) => {
							setAttributes({
								displayButton: newVal,
							});
						}}
					/>
					<TextControl
						label={ __( 'icon' ) }
						value={ icon }
						onChange={ ( newValue ) =>
							setAttributes( { icon: newValue } )
						}
						help={ __(
							'Setting an icon will override the text label.'
						) }
					/>
				</PanelBody>
			</InspectorControls>
		</>
	);

	const blockProps = useBlockProps();

		return (
			<>
				{ controls }
				<button
					type={ type }
					onClick={ ( event ) => event.preventDefault() }
					className={ classNames(
						'button',
						'wpcloud-block-form-submit-icon-button',
						'wp-block-button__link',
						blockProps.className
					) }
					{ ...blockProps }
					aria-label={ text }
				>
				<RichText
					value={ text }
					onChange={ ( newVal ) => {
						setAttributes( { text: newVal} );
					} }
					placeholder={__('label')}
					className={classNames(
						'wpcloud-block-form-submit-content',
						'wp-block-button__link',
						'wp-element-button'
					)}
				/>
				</button>
			</>
		);
};
export default Edit;
