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
				'core/group',
				{
					className: 'wpcloud-domains',
				},
				[
					[
						'core/heading',
						{
							level: 2,
							className: 'wpcloud-domains__title',
							content: __( 'Domains' ),
						},
					],
					[ 'wpcloud/site-alias-list' ],
					[ 'wpcloud/site-form-add-alias' ],
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
