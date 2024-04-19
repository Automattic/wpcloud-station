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
						{ title: 'Domain', key: 'domain_name', inline: true },
					],
					[
						'wpcloud/site-detail',
						{
							title: 'PHP Version',
							key: 'php_version',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							title: 'Data Center',
							key: 'geo_affinity',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							title: 'WP Version',
							key: 'wp_version',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							title: 'Admin Email',
							key: 'wp_admin_email',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							title: 'Admin User',
							key: 'wp_admin_user',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							title: 'IP Addresses',
							key: 'ip_addresses',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							title: 'Static 404',
							key: 'static_file_404',
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
							title: 'DB Charset',
							key: 'db_charset',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							title: 'DB Collate',
							key: 'db_collate',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							title: 'DB Password',
							key: 'db_password',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							title: 'DB File Size',
							key: 'db_file_size',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							title: 'SMTP Password',
							key: 'smtp_pass',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							title: 'Server Pool ID',
							key: 'server_pool_id',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							title: 'Atomic Client ID',
							key: 'atomic_client_id',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							title: 'Chroot Path',
							key: 'chroot_path',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							title: 'Chroot SSH Path',
							key: 'chroot_ssh_path',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							title: 'Cache Prefix',
							key: 'cache_prefix',
							inline: true,
						},
					],
					[
						'wpcloud/site-detail',
						{
							title: 'Site API Key',
							key: 'site_api_key',
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
