/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps,
	RichText,
} from '@wordpress/block-editor';
import { TextControl, PanelBody, ToggleControl, SelectControl } from '@wordpress/components';
import * as icons from '@wordpress/icons';

/**
 * Internal dependencies
 */
import { IconControl } from '@wpcloud/controls';
const Icon = icons.Icon;

export default function Edit({ attributes, setAttributes }) {
	const updateAttribute = (key) => (value) => setAttributes({ [key]: value });
	const blockProps = useBlockProps();
	const { url, tag, text, secondary, contrast, outline, icon, iconOnly, asButton } = attributes;

	const classes = classNames({
		'is-icon-only': iconOnly,
		'secondary': secondary,
		'contrast': contrast,
		'outline': outline,
	});

	let content = (
		<>
			{!iconOnly && (<RichText
				tagName={tag}
				value={text}
				onChange={updateAttribute('text')}
				placeholder={__('Item')}
			/>)}
			{icon && (
				<Icon icon={icons[icon]} />
			)}
		</>
	);

	if (url) {
		content =
			<a className={asButton ? '' : classes}>
				{content}
			</a>;

		if (asButton) {
			content = (
				<button className={classes}>
					{content}
				</button>
			);
		}
	}

	return (
		<>
			<InspectorControls>
				<PanelBody label={__('Settings')}>
					<TextControl
						label={__('URL')}
						value={url}
						onChange={(value) => setAttributes({ url: value })}
						help={__('The URL to link to. Leave blank to not render a link.')}
					/>
					<IconControl attributes={attributes}  setAttributes={setAttributes}  />
					<ToggleControl
						label={__('Icon Only')}
						checked={iconOnly}
						onChange={updateAttribute('iconOnly')}
					/>
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
						onChange={updateAttribute('tag')}
					/>
					{url && (
						<>
							<ToggleControl
								label={__('Display as Button')}
								checked={asButton}
								onChange={updateAttribute('asButton')}
							/>
							<ToggleControl
								label={ __( 'Outline' ) }
								checked={ outline }
								onChange={ updateAttribute( 'outline' ) }
							/>
							<ToggleControl
								label={ __( 'Contrast' ) }
								checked={ contrast }
								onChange={ updateAttribute( 'contrast' ) }
							/>
							<ToggleControl
								label={ __( 'Secondary' ) }
								checked={ secondary }
								onChange={ updateAttribute( 'secondary' ) }
							/>
						</>
					)}
				</PanelBody>
			</InspectorControls>
			<li {...blockProps}>
				{content}
			</li>
		</>
	);
}
