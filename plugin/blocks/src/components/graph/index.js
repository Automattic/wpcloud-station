
/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import Edit from './edit';
import metadata from './block.json';
import './editor.scss';


registerBlockType( metadata.name, {
	/**
	 * @see ./edit.js
	 */
	edit: Edit,

	/**
	 * @see ./save.js
	 */
	save: ({ attributes }) => (<div {...useBlockProps.save()} data-graph-attributes={JSON.stringify(attributes)} >Graph</div>)
} );
