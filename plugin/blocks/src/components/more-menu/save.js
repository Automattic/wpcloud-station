/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import * as icons from '@wordpress/icons';
const { Icon } = icons;

export default function save({ attributes: { icon } }) {
	const blockProps = useBlockProps.save();
	return (
		<div
			{ ...blockProps }
			className={ classNames(
				'wpcloud-more-menu',
				blockProps.className
			) }
		>
			<button className="wpcloud-more-menu__button">
				<Icon icon={ icons[icon] } />
			</button>
			<nav className="wpcloud-more-menu__nav is-closed">
				<InnerBlocks.Content />
			</nav>
		</div>
	);
}
