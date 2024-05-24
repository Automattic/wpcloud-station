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
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import * as icons from '@wordpress/icons';

const Icon = icons.Icon;

const iconOptions = Object.keys( icons ).map( ( key ) =>
	key === 'Icon' ? { label: '', key: '' } : { label: key, value: key }
);

/**
 *
 * Internal dependencies
 */
export default function ( { attributes, setAttributes, className } ) {
	const { icon } = attributes;
	const blockProps = useBlockProps();

	const controls = (
		<InspectorControls>
			<PanelBody label={ __( 'Settings' ) }>
				<SelectControl
					label={ __( 'Icon' ) }
					value={ icon }
					options={ iconOptions }
					onChange={ ( newVal ) => {
						setAttributes( { icon: newVal } );
					} }
				/>
			</PanelBody>
		</InspectorControls>
	);

	return (
		<>
			{ controls }
			<Icon
				{ ...blockProps }
				className={ classNames(
					blockProps.className,
					className,
					'wpcloud-block-icon'
				) }
				icon={ icons[ icon ] }
			/>
		</>
	);
}
