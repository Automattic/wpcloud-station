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
	InspectorControls,
	InnerBlocks,
} from '@wordpress/block-editor';
import { ToggleControl, PanelBody } from '@wordpress/components';
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
export default function Edit( {
	attributes,
	setAttributes,
	className,
	clientId,
} ) {
	const { clickToToggle, hideHeader, openOnLoad } = attributes;
	const blockProps = useBlockProps();

	const isChildSelected = useSelect( ( select ) =>
		select( 'core/block-editor' ).hasSelectedInnerBlock( clientId )
	);


	const controls = (
		<InspectorControls>
			<PanelBody title={ __( 'Form Settings' ) }>
				<ToggleControl
					label={ __( 'Click to Toggle' ) }
					checked={ clickToToggle }
					onChange={ ( newVal ) => {
						setAttributes( {
							clickToToggle: newVal,
						} );
					}}
					help={ __('If enabled clicking anywhere on the section will toggle the expandable content. If disabled use a wpcloud button with the action `wpcloud_toggle_expanding_section` to toggle the content.')}
				/>
				<ToggleControl
					label={__('Hide header when expanded')}
					checked={ hideHeader }
					onChange={ ( newVal ) => {
						setAttributes( {
							hideHeader: newVal,
						} );
					}}
				/>
				<ToggleControl
					label={__('Open by default')}
					checked={ openOnLoad }
					onChange={ ( newVal ) => {
						setAttributes( {
							openOnLoad: newVal,
						} );
					}}
				/>
			</PanelBody>
		</InspectorControls>
	);

	const template = [
		[
			'core/group',
			{
				metadata: { name: 'Expandable header' },
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
		[
			'core/group',
			{
				metadata: { name: 'Expandable content' },
				className:
					'wpcloud-block-expanding-section__content',
				layout: {
					type: "constrained"
				}
			},
			[
				[
					'core/paragraph',
					{
						content: 'Content goes here'
					}
				],
				[
					'wpcloud/button',
					{
						label: __( 'Close' ),
						action: 'wpcloud_expanding_section_toggle',
						className: 'wpcloud-block-expanding-section__toggle--close',
						type:'action'
					},
				]
			]
		],
	];

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		renderAppender: isChildSelected
			? undefined
			: InnerBlocks.ButtonBlockAppender,
		template,
	} );

	return (
		<>
			{ controls }
			<div
				{ ...innerBlocksProps }
				className={ classNames(
					className,
					'wpcloud-block-expanding-section', {
					}
				) }
			/>
		</>
	);
}
