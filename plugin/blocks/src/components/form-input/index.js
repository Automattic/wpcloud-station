/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import Edit from './edit';
import save from './save';
import metadata from './block.json';
import './style.scss';


registerBlockType( metadata.name, {
	edit: Edit,
	save,
	usesContext: [ 'wpcloud-form/isActive' ],
} );
