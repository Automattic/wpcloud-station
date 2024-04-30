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
					},
				],
				[
					'wpcloud/form',
					{
						ajax: true,
						wpcloudAction: 'site_alias_remove',
						inline: true,
						className: 'wpcloud-block-form-site-alias--remove',
					},
					[
						[
							'wpcloud/site-detail',
							{
								label: __( 'Domain Alias' ),
								name: 'site_alias',
								inline: true,
								hideLabel: true,
							},
						],
						[
							'wpcloud/button',
							{
								text: __( 'make primary' ),
								type: 'button',
								className:
									'wpcloud-block-form-site-alias--make-primary',
							},
						],
						[
							'wpcloud/button',
							{
								text: __( 'remove' ),
								icon: 'trash',
							},
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
