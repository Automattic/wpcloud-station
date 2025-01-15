/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	InspectorControls,
	RichText,
	HeadingLevelDropdown
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
	const { tag, text, section } = attributes;

	useSyncMetaName(clientId, text);

	const controls = (
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
	);

	const Container = section === 'header' ? 'header' : 'footer';
	return (
		<>
			{ controls }
			<Container { ...blockProps }>
				<RichText
					tagName={tag}
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
		const blockProps = useBlockProps.save();
		const richText = <RichText.Content tagName={tag} value={text} />
		const Container = section === 'header' ? 'header' : 'footer';

		return (
			<Container { ...useBlockProps.save() }>
				{richText}
			</Container>
		);
	}
});
