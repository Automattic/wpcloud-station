
/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { details as icon } from '@wordpress/icons';


/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';

//import save from './save';
import metadata from './block.json';

registerBlockType( metadata.name, {
	icon,
	edit,
	save,
} );
