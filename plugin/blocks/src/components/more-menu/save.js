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

export default function save({ attributes }) {
	const { icon, iconSize, position } = attributes;
	const blockProps = useBlockProps.save();
	const positionCss = position == 'left' ? {right: 0} : {left: 0};
	return (
		<div
			{ ...blockProps }
			className={ classNames(
				'wpcloud-more-menu',
				blockProps.className
			) }
		>
			<button className="wpcloud-more-menu__button">
				<Icon icon={icons[icon]} size={iconSize} />
			</button>
			<nav
				className="wpcloud-more-menu__nav is-closed"
				style={ positionCss }
			>
				<InnerBlocks.Content />
			</nav>
		</div>
	);
}
