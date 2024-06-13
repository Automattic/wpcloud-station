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
	useBlockProps,
} from '@wordpress/block-editor';
import { PanelBody,TextControl } from '@wordpress/components';
import * as icons from '@wordpress/icons';

const Icon = icons.Icon;


/**
 *
 * Internal dependencies
 */

import IconSelect from '../controls/iconSelect.js';

export default function ( { attributes, setAttributes, className } ) {
	const { icon, size } = attributes;
	const blockProps = useBlockProps();

	const controls = (
		<InspectorControls>
			<PanelBody label={ __( 'Settings' ) }>
				<IconSelect { ...{ attributes, setAttributes } } />
				<TextControl
					label={ __( 'size' ) }
					value={ size }
					onChange={ ( newVal ) => {
						setAttributes( { size: newVal } );
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
				size={ size || 24 }
			/>
		</>
	);
}
