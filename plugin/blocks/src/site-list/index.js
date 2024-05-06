/**
 * WordPress dependencies
 */
import { registerBlockVariation } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import './style.scss';
import metadata from './block.json';

registerBlockVariation( 'core/query', {
	name: metadata.name,
	title: 'Site List',
	description: 'Displays a list of WP Cloud Sites',
	isActive: ( { namespace, query } ) => {
		return namespace === metadata.name && query.postType === 'wpcloud_site';
	},
	attributes: {
		namespace: metadata.name,
		query: {
			perPage: 10,
			pages: 0,
			offset: 0,
			postType: 'wpcloud_site',
			order: 'desc',
			orderBy: 'date',
			author: '',
			search: '',
			exclude: [],
			sticky: '',
			inherit: false,
			status: 'any',
		},
	},
	scope: [ 'inserter' ],
	innerBlocks: [
		[
			'core/post-template',
			{},
			[
				[ 'wpcloud/site-card', {} ],
				[
					'core/post-author',
					{
						showAvatar: false,
						isLink: true,
						showBio: false,
						textAlign: 'center',
					},
				],
				[
					'core/post-date',
					{
						textAlign: 'center',
					},
				],
				[
					'wpcloud/site-detail',
					{
						name: 'php_version',
						label: 'PHP',
						hideLabel: true,
					},
				],
				[
					'wpcloud/site-detail',
					{
						name: 'wp_version',
						label: 'WP',
						hideLabel: true,
					},
				],
				[
					'wpcloud/site-detail',
					{
						name: 'ip_addresses',
						label: 'IP Address',
						hideLabel: true,
					},
				],
			],
		],
		[ 'core/query-pagination' ],
		[ 'core/query-no-results' ],
	],
} );
