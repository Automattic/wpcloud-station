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
			'wpcloud/table-cell',
			{ isHeader: true },
			[
				[
					'core/heading',
					{ level: 2, content: __( 'Site', 'wpcloud' ) },
				],
			],
		],

		[
			'wpcloud/table-cell',
			{ isHeader: true },
			[
				[
					'core/heading',
					{ level: 2, content: __( 'Owner', 'wpcloud' ) },
				],
			],
		],
		[
			'wpcloud/table-cell',
			{ isHeader: true },
			[
				[
					'core/heading',
					{ level: 2, content: __( 'Created', 'wpcloud' ) },
				],
			],
		],
		[
			'wpcloud/table-cell',
			{ isHeader: true },
			[
				[
					'core/heading',
					{ level: 2, content: __( 'PHP', 'wpcloud' ) },
				],
			],
		],

		[
			'wpcloud/table-cell',
			{ isHeader: true },
			[
				[
					'core/heading',
					{ level: 2, content: __( 'WP Version', 'wpcloud' ) },
				],
			],
		],

		[
			'wpcloud/table-cell',
			{ isHeader: true },
			[
				[
					'core/heading',
					{ level: 2, content: __( 'IP', 'wpcloud' ) },
				],
			],
		],
		[
			'wpcloud/table-cell',
			{ isHeader: true },
			[
				[
					'core/heading',
					{ level: 2, content: __( 'Actions', 'wpcloud' ) },
				],
			],
		],
	];

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template,
	} );

	return (
		<span
			{ ...innerBlocksProps }
			className={ classNames(
				innerBlocksProps.className,
				'wpcloud-block-site-list--header'
			) }
		/>
	);
}
