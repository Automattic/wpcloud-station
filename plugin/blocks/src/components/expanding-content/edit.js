/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
} from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import './editor.scss';

/**
 *
 * @param {Object} props               Component props.
 * @param {Object} props.attributes
 * @param {Object} props.setAttributes
 * @return {Element} Element to render.
 */
export default function Edit({
	setAttributes,
	clientId,
	context
} ) {
	const blockProps = useBlockProps();

	const openOnLoadCxt = context['wpcloud-expanding-section/openOnLoad'];

	useEffect(() => {
		setAttributes({ openOnLoad: openOnLoadCxt});
	}, [ openOnLoadCxt ] );

	const isChildSelected = useSelect( ( select ) =>
		select( 'core/block-editor' ).hasSelectedInnerBlock( clientId )
	);

	const template = [
		[ 'core/group',
			{
				metadata: { name: 'content' },
				className:
					'wpcloud-block-expanding-section__content',
				layout: {
					type: "constrained"
				}
			},
			[
				[ 'core/paragraph',
					{
						content: 'Content goes here'
					}
				],
				[ 'wpcloud/button',
					{
						label: __( 'Close' ),
						action: 'wpcloud_expanding_section_toggle',
						className: 'wpcloud-block-expanding-section__toggle--close',
						type:'action'
					},
				]
			]
		]
	];

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		renderAppender: isChildSelected
			? undefined
			: InnerBlocks.ButtonBlockAppender,
		template,
	} );

	return (
		<div
			{ ...innerBlocksProps }
		/>
	);
}
