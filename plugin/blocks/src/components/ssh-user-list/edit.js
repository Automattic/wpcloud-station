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

	const template = [
		[
			'core/group',
			{
				className: 'wpcloud-block-ssh-user-list__row',
				layout: {
					type: 'flex',
					flexWrap: 'nowrap',
				},
				metadata: {
					name: 'SSH User Row',
				},
			},
			[
				[
					'wpcloud/site-detail',
					{
						label: __( 'SSH User' ),
						name: 'ssh_user',
						inline: true,
						hideLabel: true,
						showCopyButton: true,
					},
				],
				[
					'wpcloud/more-menu',
					{
						showMenu: false,
					},
					[
						[ 'core/heading',
							{
								level: 4,
								className:
									'wpcloud-site-list-menu__title',
								content: __( 'SSH User Options' ),
							},
						],
						[ 'wpcloud/form',
							{
								ajax: true,
								wpcloudAction: 'site_ssh_user_remove',
								inline: true,
								className:
									'wpcloud-block-site-ssh-user--remove',
							},
							[
								[ 'wpcloud/icon', { icon: 'trash' } ],
								[
									'wpcloud/form-input',
									{
										type: 'hidden',
										name: 'ssh_user',
									},
								],
								[
									'wpcloud/button',
									{
										label: __( 'Remove' ),
										type: 'submit',
										style: 'text'
									},

								]
							],
						],
						[ 'core/group',
							{
								layout: {
									type: 'flex',
									flexWrap: 'wrap',
									justifyContent: 'space-between',
								}
							},
							[
								[ 'wpcloud/icon', { icon: 'update' } ],
								[
									'wpcloud/button',
									{
										label: __( 'Update' ),
										action: 'wpcloud_ssh_user_update',
										type: 'action',
										style: 'text'
									},
								]
							]
						]
					]
				],
			],
		],
	];

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template,
	} );

	return (
		<div className="wpcloud-block-ssh-user-list">
			<div
				{ ...innerBlocksProps }
				className={ classNames(
					innerBlocksProps.className,
					'wpcloud-block-site-ssh-user'
				) }
			/>
		</div>
	);
}
