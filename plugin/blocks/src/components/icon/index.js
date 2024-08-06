/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import './style.scss';

/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit,
	save,
} );
