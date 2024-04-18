/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { RichText, useBlockProps } from '@wordpress/block-editor';

export default function save( { attributes, className } ) {
	const { title, displayKey, inline, adminOnly } = attributes;
	const blockProps = useBlockProps.save();
	return (
		<div
			{ ...blockProps }
			className={ classNames( className, 'wpcloud-block-site-detail', {
				'is-inline': inline,
				'is-admin-only': adminOnly,
			} ) }
		>
			<div
				className={ classNames(
					className,
					'wpcloud-block-site-detail__title'
				) }
			>
				<RichText.Content
					tagName="h4"
					className={ 'wpcloud-block-site-detail__title-content' }
					value={ title }
				/>
			</div>
			<div className={ 'wpcloud-block-site-detail__value' }>
				{ displayKey }
			</div>
		</div>
	);
}
