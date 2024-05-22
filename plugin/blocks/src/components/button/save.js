/**
 * External dependencies
 */
import classNames from 'classnames';

import { useBlockProps, RichText } from '@wordpress/block-editor';


const Save = ( { attributes, className} ) => {
	const { icon, text, type } = attributes;
	const blockProps = useBlockProps.save();
	return (
		<div
			{ ...blockProps }
			className={classNames(
				className,
				blockProps.className,
				'wpcloud-block-form-submit',
				'wp-block-button',
			) }>
				<button
					type={type}
					className={ classNames(
						'wpcloud-block-form-submit-icon-button',
						'wp-block-button__link',
						'wp-element-button'
					) }
					aria-label={text}
				>
				<RichText.Content
					value={ text }
				/>
			</button>
		</div>
	);
};
export default Save;
