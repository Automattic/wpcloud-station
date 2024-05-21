/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl, Dashicon } from '@wordpress/components';

export default function save() {
	const blockProps = useBlockProps.save();
	return (

		<div {...blockProps} className={classNames("wpcloud-more-menu", blockProps.className)} >
			<button className="wpcloud-more-menu__button"><Dashicon icon="ellipsis" /></button>
			<nav className="wpcloud-more-menu__nav">
				<InnerBlocks.Content />
			</nav>
		</div>
	);
}
