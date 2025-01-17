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
	const { tag, text, section, level, levelOptions } = attributes;
	const tagName = 'h' + level;

	useSyncMetaName(clientId, text);

	const controls = (
		<>
			<BlockControls group="block">
				<HeadingLevelDropdown
					value={ level }
					options={ levelOptions }
					onChange={ (nextLevel) => setAttributes({ level: nextLevel }) }
				/>
			</BlockControls>
			<InspectorControls>
			<PanelBody label={__('Settings')}>
				<SelectControl
							label={__('Tag')}
							value={tag}
							options={[
								{ label: 'span', value: 'span' },
								{ label: 'h1', value: 'h1' },
								{ label: 'h2', value: 'h2' },
								{ label: 'h3', value: 'h3' },
								{ label: 'h4', value: 'h4' },
								{ label: 'h5', value: 'h5' },
								{ label: 'h6', value: 'h6' },
							]}
							onChange={(value) => setAttributes({ tag: value} )}
						/>
			</PanelBody >
		</InspectorControls>
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

		const { tag, text, section } = attributes;
		if ( !text ) {
			return null;
		}
		const richText = <RichText.Content tagName={tag} value={text} />
		const Container = section === 'header' ? 'header' : 'footer';

		return (
			<Container { ...useBlockProps.save() }>
				{richText}
			</Container>
		);
	}
});
