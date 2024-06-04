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
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';

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
	context,
	isSelected,
	className
} ) {
	const blockProps = useBlockProps();

	const isChildSelected = useSelect( ( select ) =>
		select( 'core/block-editor' ).hasSelectedInnerBlock( clientId )
	);

	const hideOnOpenCxt = context['wpcloud-expanding-section/hideHeader'];
	const openOnLoadCxt = context['wpcloud-expanding-section/openOnLoad'];

	useEffect(() => {
		setAttributes({ hideOnOpen: hideOnOpenCxt, openOnLoad: openOnLoadCxt});
	}, [hideOnOpenCxt, openOnLoadCxt]);


	const template = [
		[ 'core/group',
			{
				metadata: { name: 'header' },
				className:
					'wpcloud-block-expanding-section__header',
				layout: {
					type: 'flex',
					flexWrap: 'nowrap',
					justifyContent: 'space-between',
				},
			},
			[
				[
					'core/heading',
					{
						content: __( 'Hello' ),
						level: 3,
					},
				],
				[
					'wpcloud/button',
					{
						label: __( 'Open' ),
						action: 'wpcloud_expanding_section_toggle',
						className: 'wpcloud-block-expanding-section__toggle--open',
						type: 'action'
					},
			 	]
			]
		],

	];

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		renderAppender: isChildSelected || !isSelected
			? undefined
			: InnerBlocks.ButtonBlockAppender,
		template,
	} );

	return (
		<div
			{...innerBlocksProps}
			className={ classNames(
				className,
				'wpcloud-block-expanding-section__header',
				innerBlocksProps.className,
			)}
		/>
	);
}
