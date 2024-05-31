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
export default function Edit() {
	const blockProps = useBlockProps();

	// @TODO: Make sure the required fields are not mutable.
	const template = [
		[
			'core/group',
			{
				className: 'wpcloud-ssh-users',
			},
			[
				[
					'core/heading',
					{
						level: 3,
						className: 'wpcloud-ssh-users__title',
						content: __('SSH Users'),
					},
				],
				[ 'wpcloud/expanding-section',
					{
						hideHeader: true,
						className: 'wpcloud-ssh-user-form--expanding-section',
						metadata: { name: 'SSH User Form' }
					},
					[
						[ 'wpcloud/expanding-header', {},
							[
								['wpcloud/button',
									{
										type: 'action',
										action: 'wpcloud_expanding_section_toggle',
										label: __('Add SSH User'),
										isPrimary: false
									}
								]
							]
						],
						['wpcloud/expanding-content',
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
								[ 'wpcloud/ssh-user-add']
							]
						]
					]
				],
				[ 'core/spacer', { height: '30px' } ],
				['wpcloud/ssh-user-list'],
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
				innerBlocksProps.className,
				'wpcloud-block-ssh-users'
			) }
		/>
	);
}
