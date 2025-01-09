/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import metadata from './block.json';

const render = (save) => ({ attributes, setAttributes }) => {
	const { tag, text, section } = attributes;
	const richText = save ? (
		<RichText.Content tagName={tag} value={text} />
	) : (
		<RichText
			tagName={tag}
			value={text}
			onChange={(value) => setAttributes({ text: value })}
			placeholder={ section === 'header' ? __( 'Header' ) : __( 'Footer' ) }
		/>
	);

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

	if ( section === 'header' ) {
		return (
			<>
				{ save ? null : controls }
				<header>
					{richText}
				</header>
			</>
		);
	}
	return (
		<>
			{ save ? null : controls }
			<footer>
				{richText}
			</footer>
		</>
	);
}


registerBlockType(metadata.name, {
	edit: render(false),
	save: render(true)
});
