/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { RichText, useBlockProps } from '@wordpress/block-editor';
export default function save( { attributes, className } ) {
	const { title, displayKey, inline, adminOnly, hideTitle } = attributes;
	const blockProps = useBlockProps.save();
	return (
		<span { ...blockProps }>
		<div

			className={ classNames( className, 'wpcloud-block-site-detail', {
				'is-inline': inline,
				'is-admin-only': adminOnly,
			} ) }
		>
			{ hideTitle ? null : (
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
			) }
			<div className={ 'wpcloud-block-site-detail__value' }>
				{ displayKey }
			</div>
			</div>
		</span>
	);
}
