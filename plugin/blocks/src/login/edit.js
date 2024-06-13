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
				wpcloudAction: 'login',
				redirect: '/sites',
			},
			[
				[
					'wpcloud/form-input',
					{
						type: 'text',
						label: __( 'Username or Email Address' ),
						name: 'log',
						placeholder: __( '' ),
						required: true,
					},
				],
				[
					'wpcloud/form-input',
					{
						type: 'password',
						label: __( 'Password' ),
						name: 'pwd',
						required: true,
					},
				],
				[
					'wpcloud/form-input',
					{
						type: 'checkbox',
						name: 'rememberme',
						label: __( 'Remember Me' ),
					},
				],
				[
					'wpcloud/button',
					{
						type: 'submit',
						label: __( 'Login In' ),
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
				'wpcloud-new-site-form',
				innerBlocksProps?.className
			) }
		/>
	);
}
