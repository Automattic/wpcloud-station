/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';
import * as icons from '@wordpress/icons';
const { Icon } = icons;

export default function save( { attributes, className } ) {
	const { icon } = attributes;
	const blockProps = useBlockProps.save();

	return (
		<Icon
				{ ...blockProps }
				className={ classNames(
					blockProps.className,
					className,
					'wpcloud-block-icon',
				) }
				icon={ icons[ icon ] }
			/>
	);
}
