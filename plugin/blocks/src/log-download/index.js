/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import { inputTemplate } from '../utils/templates';

const required = [
	{ name: 'type', label: __('Type'), required: true, hint: '', type: 'select', options: { server: __('Server'), error: __('Error') } },
	{ name: 'log_start', label: __('Start Time'),required: true, hint: __( 'Logs are only guaranteed for 28 days. A start date before that may return incomplete data.' ), type: 'datetime' },
	{ name: 'log_end', label: __('End Time'), required: true, hint: '', type: 'datetime', value: '2024-08-29T14:40' },
]
const filters = [

	{ name: 'page_size', label: __('Page Size'), hint: __( 'The maximum number of records to retrieve in a single request. Defaults to 500. Max of 10000.' ), type: 'number', placeholder: __('Enter page size') },
	{ name: 'scroll_id', label: __('Scroll ID'), hint: __( 'String used to specify the next page of data for large queries; the same query arguments as the initial query must be provided with the scroll_id on each subsequent request.' ), type: 'text', placeholder: __('Enter scroll ID') },
	{ name: 'sort_order', label: __('Sort Order'), hint: '', type: 'select', options: { asc: __('Ascending'), desc: __('Descending') } },
	{ name: 'filter_error', label: __('Filter'), hint: '', type: 'select', options: { all: __('All'), fatal: __('Fatal'), error: __('Error'), warning: __('Warning') } },
	{ name: 'filter_server_cached', label: __('Filter Server Cached'), hint: '', type: 'select', options: { all: __('All'), cached: __('Cached'), uncached: __('Uncached') } },
	{ name: 'filter_server_renderer', label: __('Filter Renderer'), hint: '', type: 'select', options: { all: __('All'), renderer: __('Renderer'), not_renderer: __('Not Renderer') } },
	{ name: 'filter_server_request_type', label: __('Filter Request Type'), hint: '', type: 'text', placeholder: __('Enter request type') },
	{ name: 'filter_server_status', label: __('Filter Status'), hint: '', type: 'text', placeholder: '200' },
	{ name: 'filter_server_user_ip', label: __('Filter User IP'), hint: '', type: 'text', placeholder: __('127.0.0.0') },
];

const template = [
	[ 'wpcloud/site-details', { metadata: { name: 'Download logs form' } },
		[
			[ 'core/heading', { level: 2, content: __('Download Logs') } ],
			[
				'wpcloud/form',
				{
					ajax: true,
					wpcloudAction: 'log_download',
				},
				[
					...required.map(inputTemplate),
					[ 'wpcloud/expanding-section', { metadata: { name: 'Log Filters' }, clickToToggle: false, hideHeader: false },
						[
							[ 'wpcloud/expanding-header', { className: 'click-to-toggle' },
							[
								['core/heading', { level: 3, content: __('Filters'),  }],
							],
							],
							[ 'wpcloud/expanding-content', {},
								[
									...filters.map(inputTemplate)
								]
							],
						]
					],
					[ 'wpcloud/button', { label: __('Download'), type: 'submit' } ],
				]
			],
		]
	]
];

registerBlockType(metadata.name, {
	edit: () => <InnerBlocks template={template} />,
	save: () => <InnerBlocks.Content />,
});
