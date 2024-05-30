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

	const template = useMemo(
		() => [
			[
				'wpcloud/form',
				{
					ajax: true,
					wpcloudAction: 'site_alias_add',
					className: 'wpcloud-block-form--site-alias-add',
				},
				[
					[
						'wpcloud/form-input',
						{
							type: 'text',
							label: __( 'Add a Domain' ),
							name: 'site_alias',
							placeholder: __( 'example.com' ),
							required: true,
						},
					],
					[
						'wpcloud/button',
						{
							label: __( 'Add' ),
							type: 'submit',
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
