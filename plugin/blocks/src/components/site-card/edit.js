/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Dashicon } from '@wordpress/components';

/**
 * Internal dependencies
 */
import './style.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, className } ) {
	const { placeholderThumbnail } = attributes;
	return (
		<div
			{ ...useBlockProps() }
			className={ classNames(
				useBlockProps.className,
				'wp-block-wpcloud-site-card',
				className
			) }
		>
			<img src={ placeholderThumbnail } />
			<h2 className="site-title">
				<a href="#">{ __( 'Site Name', 'site-card' ) }</a>
			</h2>
			<h3 className="site-url">
				<a href="#" target="_blank">
					<span>{ __( 'Site Domain', 'site-card' ) }</span>
					<Dashicon icon="external" />
				</a>
			</h3>
		</div>
	);
}
