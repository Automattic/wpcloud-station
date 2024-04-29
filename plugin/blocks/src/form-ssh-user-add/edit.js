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
			'wpcloud/form',
			{
				ajax: true,
				wpcloudAction: 'site_ssh_user_add',
			},
			[
				[
					'wpcloud/form-input',
					{
						type: 'password',
						label: __( 'Password' ),
						name: 'pass',
					},
				],
				[
					'wpcloud/form-input',
					{
						type: 'textarea',
						name: 'pkey',
						label: __( 'Public Key' ),
					},
				],
				[
					'wpcloud/button',
					{
						text: __( 'Add' ),
					},
				],
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
				'wpcloud-form-site-ssh-user--add',
				innerBlocksProps?.className
			) }
		/>
	);
}
