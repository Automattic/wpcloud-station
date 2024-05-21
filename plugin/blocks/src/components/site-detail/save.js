/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { RichText, useBlockProps } from '@wordpress/block-editor';
export default function save( { attributes, className } ) {
	const { label, inline, adminOnly, hideLabel, refreshLink } = attributes;
	const blockProps = useBlockProps.save();
	return (
		<div
			{ ...blockProps }
			className={ classNames(
				className,
				blockProps.className,
				'wpcloud-block-site-detail',
				{
					'is-inline': inline,
					'is-admin-only': adminOnly,
					'refresh-link': refreshLink,
				}
			) }
		>
			{ hideLabel ? null : (
				<div
					className={ classNames(
						className,
						'wpcloud-block-site-detail__title'
					) }
				>
					<RichText.Content
						tagName="h5"
						className={ 'wpcloud-block-site-detail__title-content' }
						value={ label }
					/>
				</div>
			) }
			<div className={ 'wpcloud-block-site-detail__value' }>
				{ `{ ${ label } }` }
			</div>
		</div>
	);
}
