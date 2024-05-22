/**
 * External dependencies
 */
import classNames from 'classnames';

import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';
import { Dashicon } from '@wordpress/components';

const Save = ( { attributes } ) => {
	const { icon, text, type } = attributes;
	const blockProps = useBlockProps.save();
	return (
		<div
			className={ classNames(
				'wpcloud-block-form-submit-wrapper',
				blockProps.className
			) }
			{ ...blockProps }
		>
			<InnerBlocks.Content />
		</div>
	);
};
export default Save;
