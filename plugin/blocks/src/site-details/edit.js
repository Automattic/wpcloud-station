/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import './editor.scss';

const TEMPLATE = [
	[
		'core/columns',
		{
			verticalAlignment: 'center',
			isStackedOnMobile: true,
			width: '100%',
		},
		[
			[
				'core/column',
				{
					verticalAlignment: 'top',
				},
				[],
			],
			[
				'core/column',
				{
					verticalAlignment: 'top',
				},
				[
					[
						'wpcloud/site-detail',
						{ label: 'Domain', name: 'domain_name', inline: true },
					],
					[
						'wpcloud/site-detail',
						{
							label: 'PHP Version',
							name: 'php_version',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							label: 'Data Center',
							name: 'geo_affinity',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							label: 'WP Version',
							name: 'wp_version',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							label: 'Admin Email',
							name: 'wp_admin_email',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							label: 'Admin User',
							name: 'wp_admin_user',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							label: 'IP Addresses',
							name: 'ip_addresses',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							label: 'Static 404',
							name: 'static_file_404',
							inline: true,
						},
					],
				],
			],
			[
				'core/column',
				{
					verticalAlignment: 'top',
				},
				[
					[
						'wpcloud/site-detail',
						{
							label: 'DB Charset',
							name: 'db_charset',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							label: 'DB Collate',
							name: 'db_collate',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							label: 'DB Password',
							name: 'db_password',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							label: 'DB File Size',
							name: 'db_file_size',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							label: 'SMTP Password',
							name: 'smtp_pass',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							label: 'Server Pool ID',
							name: 'server_pool_id',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							label: 'Atomic Client ID',
							name: 'atomic_client_id',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							label: 'Chroot Path',
							name: 'chroot_path',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							label: 'Chroot SSH Path',
							name: 'chroot_ssh_path',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							label: 'Cache Prefix',
							name: 'cache_prefix',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							label: 'Site API name',
							name: 'site_api_key',
							inline: true,
						},
					],
				],
			],
			[
				'core/column',
				{
					verticalAlignment: 'top',
				},
				[],
			],
		],
	],
];

/*
 *
 * @return {Element} Element to render.
 */
export default function Edit() {
	const blockProps = useBlockProps();

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template: TEMPLATE,
	} );

	return <div { ...innerBlocksProps } className="wpcloud-all-site-details" />;
}
