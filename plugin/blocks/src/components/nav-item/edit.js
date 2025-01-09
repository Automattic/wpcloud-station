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
import { TextControl, PanelBody, ToggleControl } from '@wordpress/components';
import * as icons from '@wordpress/icons';

/**
 * Internal dependencies
 */
import IconControl from '../controls/iconControl.js';
const Icon = icons.Icon;

const Wrap = ({condition, wrapper, children}) => condition ? wrapper(children): children;

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
				<Wrap
					condition={asButton}
					wrapper={(content) => (<button classNames={classes}> {content}</button>)}>
					<Wrap
						condition={url}
						wrapper={(content) => (<a className={!asButton && classes}> {content}</a>)}>
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
					</Wrap>
				</Wrap>
			</li>
		</>
	);
}
