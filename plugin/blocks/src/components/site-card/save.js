/**
 * External dependencies
 */
import classNames from 'classnames';

/**
*	WordPress dependencies
*/
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {Element} Element to render.
 */
export default function save({ attributes, className }) {
	const { placeholderThumbnail } = attributes;
	const blockProps = useBlockProps.save();
	return (
		<div { ...blockProps } className={ classNames( blockProps.className,'wp-block-wpcloud-site-card', className )}>
			<img src={ placeholderThumbnail } />
			<h2 className="site-title">
				<a href="#">{ __( 'Site Name', 'site-card' ) }</a>
			</h2>
			<h3 className="site-url">
				<a href="#" target="_blank">
					<span>{ __( 'Site Domain', 'site-card' ) }</span>
					<span className="dashicons dashicons-external" />
				</a>
			</h3>
		</div>
	);
}
