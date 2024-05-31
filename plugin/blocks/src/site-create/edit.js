/**
 * External dependencies
 */
import classNames from 'classnames';

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

function formatOptions( data ) {
	const options = [];
	for ( const key in data ) {
		const value = data[ key ];
		options.push( { value, label: value } );
	}
	return options;
}

/*
 *
 * @return {Element} Element to render.
 */
export default function Edit() {
	const blockProps = useBlockProps();

	const phpVersionOptions = window.wpcloud?.phpVersions;
	const dataCenterOptions = window.wpcloud?.dataCenters;

	const template = useMemo(
		() => [
			[
				'wpcloud/form',
				{
					ajax: true,
					wpcloudAction: 'site_create',
				},
				[
					[
						'core/heading',
						{
							level: 3,
							content: __( 'Add Site' ),
						},
					],
					[
						'wpcloud/form-input',
						{
							type: 'text',
							label: '',
							name: 'site_name',
							placeholder: __( 'Enter site name' ),
							required: true,
							metadata: { name: 'Site Name' },
						},
						[
							[
								'wpcloud/expanding-section',
								{
									clickToToggle: true,
									hideHeader: false,
									openOnLoad: true,
								},
								[
									[
										'wpcloud/expanding-header',
										{
											metadata: {
												name: 'Site name label',
											},
											openOnLoad: true,
										},
										[
											[
												'core/group',
												{
													metadata: { name: 'label' },
													className:
														'wpcloud-block-expanding-section__header',
													layout: {
														type: 'flex',
														flexWrap: 'nowrap',
														justifyContent: 'left',
													},
												},
												[
													[
														'core/paragraph',
														{
															content:
																__( 'Name' ),
														},
													],
													[
														'wpcloud/button',
														{
															type: 'action',
															style: 'text',
															label: 'Open',
															addIcon: true,
															iconOnly: true,
															action: 'wpcloud_expanding_section_toggle',
															className:
																'wpcloud-block-expanding-section__toggle--open',
														},
														[
															[
																'wpcloud/icon',
																{
																	icon: 'info',
																},
															],
														],
													],
												],
											],
										],
									],
									[
										'wpcloud/expanding-content',
										{
											metadata: {
												name: 'Site name info',
											},
											openOnLoad: true,
										},
										[
											[
												'core/group',
												{
													metadata: {
														name: 'content',
													},
													className:
														'wpcloud-block-expanding-section__content',
													layout: {
														type: 'constrained',
													},
												},
												[
													[
														'core/paragraph',
														{
															content:
																'We’ll choose a temporary domain for you to get your started. You’ll be able to change this later.',
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
					[
						'wpcloud/form-input',
						{
							type: 'select',
							label: __( 'PHP Version' ),
							name: 'php_version',
							options: formatOptions( phpVersionOptions ),
							metadata: { name: 'PHP Version' },
						},
					],
					[
						'wpcloud/form-input',
						{
							type: 'select',
							name: 'data_center',
							label: __( 'Data Center' ),
							options: formatOptions( dataCenterOptions ),
							metadata: { name: 'Data Center' },
						},
					],

					[
						'wpcloud/form-input',
						{
							type: 'select',
							name: 'site_owner_id',
							label: __( 'Owner' ),
							adminOnly: true,
							options: [ { value: '1', label: 'Site Owner' } ],
							metadata: { name: 'Owner' },
						},
					],
					[
						'wpcloud/form-input',
						{
							type: 'password',
							name: 'admin_pass',
							label: __( 'WP Admin Password' ),
							placeholder: __( '••••••••' ),
							metadata: { name: 'WP Admin Password' },
						},
					],
					[
						'wpcloud/form-input',
						{
							type: 'checkbox',
							name: 'toc',
							label: __(
								'Any <a href="https://en.wikipedia.org/wiki/Terms_of_service">terms and conditions</a> that we might need to add.'
							),
							metadata: { name: 'TOC' },
						},
					],
					[
						'wpcloud/button',
						{
							label: __( 'Create Site' ),
							type: 'submit',
							metadata: { name: 'Create Site Button' },
						},
					],
				],
			],
		],
		[ dataCenterOptions, phpVersionOptions ]
	);

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template,
	} );

	return (
		<div
			{ ...innerBlocksProps }
			className={ classNames(
				'wpcloud-new-site-form',
				innerBlocksProps?.className
			) }
		/>
	);
}
