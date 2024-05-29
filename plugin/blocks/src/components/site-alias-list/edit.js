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
				className: 'wpcloud-block-site-alias-list',
				metadata: {
					name: 'Site Alias List',
				},
			},
			[
				[
					'core/group',
					{
						metadata: { name: 'Primary Domain Row' },
						className:
							'wpcloud-block-site-alias-list__row wpcloud-block-site-alias-list__row--primary',
						layout: {
							type: 'flex',
							flexWrap: 'nowrap',
							justifyContent: 'space-between',
						},
					},
					[
						[
							'wpcloud/site-detail',
							{
								label: __( 'Primary Domain' ),
								name: 'domain_name',
								inline: true,
								hideLabel: true,
								className:
									'wpcloud-block-site-alias-list__item--primary',
								metadata: {
									name: __( 'Primary Domain' ),
								},
							},
						],
						[
							'core/paragraph',
							{
								metadata: { name: 'Primary Domain Badge' },
								content: __( 'Primary' ),
								className:
									'wpcloud-block-site-alias-list__item--primary-badge',
							},
						],
					],
				],
				[
					'core/group',
					{
						className:
							'wpcloud-block-site-alias-list__row wpcloud-block-dynamic-row',
						layout: {
							type: 'flex',
							flexWrap: 'wrap',
							justifyContent: 'space-between',
						},
						metadata: {
							name: 'Alias Row',
						},
					},
					[
						[
							'wpcloud/site-detail',
							{
								label: __( 'Alias' ),
								name: 'alias',
								inline: true,
								hideLabel: true,
								metadata: {
									name: __( 'Site Alias' ),
								},
							},
						],

						[
							'wpcloud/more-menu',
							{
								showMenu: false,
							},
							[
								[
									'core/heading',
									{
										level: 4,
										className:
											'wpcloud-site-list-menu__title',
										content: __( 'Site Details' ),
									},
								],
								[
									'wpcloud/form',
									{
										ajax: true,
										wpcloudAction:
											'site_alias_make_primary',
										inline: true,
										className:
											'wpcloud-block-site-alias-list--remove',
									},
									[
										[
											'wpcloud/icon',
											{ icon: 'starEmpty' },
										],
										[
											'wpcloud/form-input',
											{
												type: 'hidden',
												name: 'site_alias',
											},
										],
										[
											'wpcloud/button',
											{
												text: __( 'Make Primary' ),
												asButton: false,
											},
										],
									],
								],
								[
									'wpcloud/form',
									{
										ajax: true,
										wpcloudAction:
											'site_alias_make_primary',
										inline: true,
										className:
											'wpcloud-block-site-alias-list--remove',
									},
									[
										[ 'wpcloud/icon', { icon: 'trash' } ],
										[
											'wpcloud/form-input',
											{
												type: 'hidden',
												name: 'site_alias',
											},
										],
										[
											'wpcloud/button',
											{
												text: __( 'Remove Domain' ),
												asButton: false,
											},
										],
									],
								],
							],
						],
					],
				],
			],
		],
	];

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template,
	} );

	return (
		<div className="wpcloud-block-site-alias-list--wrapper">
			<div
				{ ...innerBlocksProps }
				className={ classNames(
					innerBlocksProps.className,
					'wpcloud-block-site-alias-list'
				) }
			/>
		</div>
	);
}
