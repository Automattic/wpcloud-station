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

	const phpVersionOptions = useMemo(
		() => window.wpcloud?.phpVersions,
		[],
		[]
	);
	const dataCenterOptions = useMemo(
		() => window.wpcloud?.dataCenters,
		[],
		[]
	);

	// @TODO: Make sure the required fields are not mutable.
	const template = useMemo(
		() => [
			[
				'wpcloud/form',
				{
					ajax: true,
					wpcloudAction: 'create_site',
				},
				[
					[
						'core/heading',
						{
							level: 3,
							content: __( 'New Site' ),
						},
					],
					[
						'wpcloud/form-input',
						{
							type: 'text',
							label: __( 'Name' ),
							name: 'site_name',
							placeholder: __( 'Enter site name' ),
							required: true,
						},
					],
					[
						'wpcloud/form-input',
						{
							type: 'select',
							label: __( 'PHP Version' ),
							name: 'php_version',
							options: formatOptions( phpVersionOptions ),
							required: true,
						},
					],
					[
						'wpcloud/form-input',
						{
							type: 'select',
							name: 'data_center',
							label: __( 'Data Center' ),
							options: formatOptions( dataCenterOptions ),
							required: true,
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
						},
					],
					[
						'wpcloud/button',
						{
							text: __( 'Create Site' ),
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
