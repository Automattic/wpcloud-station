/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockVariation } from '@wordpress/blocks';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style.scss';

/**
 * Internal dependencies
 */
import metadata from './block.json';

registerBlockVariation( 'core/query', {
    name: metadata.name,
    title: 'Site List',
    description: 'Displays a list of WP Cloud Sites',
    isActive: ( { namespace, query } ) => {
        return (
            namespace === metadata.name
            && query.postType === 'wpcloud_site'
        );
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
        },
    },
    scope: [ 'inserter' ],
	innerBlocks: [
		[
			'core/post-template',
			{},
			[ [ 'core/post-title' ], [ 'core/post-excerpt' ] ],
		],
		[ 'core/query-pagination' ],
		[ 'core/query-no-results' ],
	],
    }
);
