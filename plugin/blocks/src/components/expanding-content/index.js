/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';
import metadata from './block.json';

registerBlockType( metadata.name, {
	usesContext: [ 'wpcloud-expanding-section/openOnLoad', 'wpcloud-expanding-section/hideContent', 'wpcloud-expanding-section/hideHeader'],
	edit,
	save,
} );
