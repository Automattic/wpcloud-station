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
import './editor.scss';
import { Placeholder } from './placeholder.js';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit( { className, context } ) {

	const blockProps = useBlockProps();
	const {
		postId,
		postTitle,
		primaryDomain
	} = context;

	return (
		<div
			{ ...blockProps }
			className={ classNames(
				blockProps.className,
				'wp-block-wpcloud-site-card',
				className
			) }
		>
			<Placeholder
				postId={postId}
				name={postTitle}
			/>
			<h2 className="site-title">
				<a href="#" target="_blank">{postTitle}</a>
			</h2>
			<h3 className="site-url">
				<a href="#" target="_blank">
					<span>{ primaryDomain }</span>
				</a>
			</h3>
		</div>
	);
}
