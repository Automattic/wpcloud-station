/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { Icon, copySmall, seen } from '@wordpress/icons';

export default function save( { attributes, className } ) {
	const { label, inline, adminOnly, hideLabel, obscureValue, revealButton , showCopyButton } =
		attributes;
	const blockProps = useBlockProps.save();
	return (
		<div
			{ ...blockProps }
			className={ classNames(
				className,
				'wpcloud-block-site-detail',
				{
					'is-inline': inline,
					'is-admin-only': adminOnly,
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
			<div
				className={ classNames(
					'wpcloud-block-site-detail__value-container',
					{
						'copy-to-clipboard': showCopyButton,
						'is-obscured': obscureValue,
					}
				) }
			>
				<div className={ 'wpcloud-block-site-detail__value' }>
					{ `{ ${ label } }` }
				</div>
				{ ( obscureValue && revealButton ) && (
					<Icon
						className="wpcloud-reveal-value"
						icon={seen}
					/>
				) }
				{ showCopyButton && (
					<Icon
						className="wpcloud-copy-to-clipboard"
						icon={ copySmall }
					/>
				) }
			</div>
		</div>
	);
}
