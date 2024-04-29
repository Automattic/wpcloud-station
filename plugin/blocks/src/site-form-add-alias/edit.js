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
					'wpcloud/form',
					{
						ajax: true,
						wpcloudAction: 'site_alias_add',
						inline: true,
						className: 'wpcloud-block-form--site-alias-add',
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
							'wpcloud/button',
							{
								text: __( 'Add' ),
								inline: true,
							},
						],
					],
				],
			],
		[]
	);

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template,
	} );

	return (
		<div
			{ ...innerBlocksProps }
			className={ classNames(
				innerBlocksProps.className,
				'wpcloud-block-site-alias-add'
			) }
		/>
	);
}
