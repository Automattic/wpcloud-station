/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	InspectorControls,
	RichText,
	HeadingLevelDropdown,
	BlockControls
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { heading as icon } from '@wordpress/icons';


/**
 * Internal dependencies
 */
import metadata from './block.json';

import { useSyncMetaName } from '@wpcloud/hooks';

const edit = ( { clientId, attributes, setAttributes } ) => {
	const blockProps = useBlockProps();
	const { text, section, level, levelOptions } = attributes;
	const tagName = 'h' + level;

	useSyncMetaName(clientId, text || section[0].toUpperCase() + section.slice(1));

	const controls = (
		<>
			<BlockControls group="block">
				<HeadingLevelDropdown
					value={ level }
					options={ levelOptions }
					onChange={ (nextLevel) => setAttributes({ level: nextLevel }) }
				/>
			</BlockControls>
	</>
	);

	const Container = section === 'header' ? 'header' : 'footer';
	return (
		<>
			{ controls }
			<Container { ...blockProps }>
				<RichText
					tagName={tagName}
					value={text}
					onChange={(value) => setAttributes({ text: value })}
					placeholder={ section === 'header' ? __( 'Header' ) : __( 'Footer' ) }
				/>
			</Container>
		</>
	);
}

registerBlockType(metadata.name, {
	icon,
	edit,
	save: ({ attributes }) => {

		const { level, text, section } = attributes;
		if ( !text ) {
			return null;
		}
		const richText = <RichText.Content tagName={`h${level}`} value={text} />
		const Section = section === 'header' ? 'header' : 'footer';

		return (
			<Section { ...useBlockProps.save() }>
				{richText}
			</Section>
		);
	}
});
