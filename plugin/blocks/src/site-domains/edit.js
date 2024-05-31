/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import './editor.scss';

/*
 *
 * @return {Element} Element to render.
 */
export default function Edit( { className } ) {
	const blockProps = useBlockProps();

	// @TODO: Make sure the required fields are not mutable.
	const template = [
		[
			'core/group',
			{
				className: 'wpcloud-domains',
			},
			[
				[
					'core/heading',
					{
						level: 3,
						className: 'wpcloud-domains__title',
						content: __( 'Domains' ),
					},
				],
				[
					'wpcloud/expanding-section',
					{
						hideHeader: true,
						className: 'wpcloud-site-alias-add--expanding-section',
					},
					[
						[
							'wpcloud/expanding-header',
							{},
							[
								[
									'wpcloud/button',
									{
										type: 'action',
										action: 'wpcloud_expanding_section_toggle',
										label: __( 'Add Domain' ),
										isPrimary: false,
									},
								],
							],
						],
						[
							'wpcloud/expanding-content',
							{},
							[
								[
									'wpcloud/button',
									{
										label: __( 'Cancel' ),
										action: 'wpcloud_expanding_section_toggle',
										style: 'text',
										type: 'action',
										className:
											'wpcloud-site-alias-add__cancel',
									},
								],

								[ 'wpcloud/site-alias-add' ],
							],
						],
					],
				],
				[ 'wpcloud/site-alias-list' ],
			],
		],
	];

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template,
	} );

	return (
		<div
			{ ...innerBlocksProps }
			className={ classNames(
				className,
				innerBlocksProps.className,
				'wpcloud-block-site-alias-add'
			) }
		/>
	);
}
