/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { useMemo } from '@wordpress/element';

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
	const template = useMemo(
		() => [
			[
				'core/group',
				{},
				[
					[
						'core/heading',
						{
							level: 3,
							content: __( 'Domains' ),
							className: 'wpcloud-block-site-alias-heading',
						},
					],
					[
						'wpcloud/form',
						{
							ajax: true,
							wpcloudAction: 'site-alias-remove',
							inline: true,
						},
						[
							[
								'wpcloud/site-detail',
								{
									label: __( 'example.com' ),
									name: 'site_alias',
									inline: true,
									hideLabel: true,
								},
							],
							[
								'wpcloud/form-submit-button',
								{
									text: __( 'remove' ),
									icon: 'trash',
								},
							],
						],
					],
					[
						'wpcloud/form',
						{
							ajax: true,
							wpcloudAction: 'site-alias-add',
							inline: true,
						},
						[
							[
								'wpcloud/site-detail',
								{
									label: __( 'another.example.com' ),
									name: 'site_alias',
									inline: true,
									hideLabel: true,
								},
							],
							[
								'wpcloud/form-submit-button',
								{
									text: __( 'remove' ),
									icon: 'trash',
								},
							],
						],
					],
					[
						'wpcloud/form',
						{
							ajax: true,
							wpcloudAction: 'add_alias',
							inline: true,
						},
						[
							[
								'wpcloud/form-input',
								{
									type: 'text',
									label: __( 'Add a Domain' ),
									name: 'site_alias',
									placeholder: __( 'new.example.com' ),
									required: true,
									inline: true,
								},
							],
							[
								'wpcloud/form-submit-button',
								{
									text: __( 'Add' ),
									inline: true,
								},
							],
						],
					],
				],
			],
		],
		[]
	);

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template,
	} );

	return <div { ...innerBlocksProps } className="wpcloud-block-site-alias" />;
}
