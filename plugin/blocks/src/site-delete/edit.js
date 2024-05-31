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
				wpcloudAction: 'site_delete',
			},
			[
				[
					'wpcloud/button',
					{
						className: 'wpcloud-button-site-delete',
						label: __('Delete Site'),
						type: 'submit',
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
				'wpcloud-block-site-delete',
				innerBlocksProps?.className
			) }
		/>
	);
}
