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
import { PanelBody } from '@wordpress/components';
import * as icons from '@wordpress/icons';

const Icon = icons.Icon;


/**
 *
 * Internal dependencies
 */

import IconControl from '../controls/iconControl.js';

export default function ( { attributes, setAttributes, className } ) {
	const { icon, iconSize } = attributes;
	const blockProps = useBlockProps();

	const controls = (
		<InspectorControls>
			<PanelBody label={ __( 'Settings' ) }>
				<IconControl { ...{ attributes, setAttributes } } />
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
				size={ iconSize}
			/>
		</>
	);
}
