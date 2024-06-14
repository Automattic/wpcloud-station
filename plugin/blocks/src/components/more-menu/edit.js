/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	InnerBlocks,
} from '@wordpress/block-editor';
import { ToggleControl, PanelBody, SelectControl } from '@wordpress/components';
import * as icons from '@wordpress/icons';
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
const Icon = icons.Icon;
/**
 * Internal dependencies
 */
import IconControl from '../controls/iconControl.js';
import './editor.scss';

/**
 *
 * @param {Object} props               Component props.
 * @param {Object} props.attributes
 * @param {Object} props.setAttributes
 * @return {Element} Element to render.
 */
export default function Edit( {
	attributes,
	setAttributes,
	className,
	clientId,
	isSelected
} ) {
	const { showMenu, icon, iconSize, position } = attributes;
	const blockProps = useBlockProps();

	const isChildSelected = useSelect( ( select ) =>
		select( 'core/block-editor' ).hasSelectedInnerBlock( clientId )
	);
	const shouldShowMenu = isSelected || isChildSelected;
	useEffect(() => {
		setAttributes( { showMenu: shouldShowMenu } );
	}, [ shouldShowMenu, setAttributes ]);

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		renderAppender: isChildSelected
			? undefined
			: InnerBlocks.DefaultBlockAppender,
	} );

	const controls = (
		<InspectorControls>
			<PanelBody title={__('Form Settings')}>
				<IconControl { ...{ attributes, setAttributes } } />
				<ToggleControl
					label={ __( 'Show Menu' ) }
					checked={ showMenu }
					onChange={ ( newVal ) => {
						setAttributes( {
							showMenu: newVal,
						} );
					} }
				/>
				<SelectControl
					label={__('Position')}
					value={position}
					options={[
						{ label: 'Left', value: 'left' },
						{ label: 'Right', value: 'right' },
					] }
					onChange={(newVal) => {
						setAttributes({ position: newVal });
					}}
					hint={__('Select the position of the menu')}
				/>

			</PanelBody>
		</InspectorControls>
	);

	const positionCss = position === 'left' ? { right: 0} : {left: 0};

	return (
		<>
			{controls}
			<div
				className={ classNames(
					className,
					'wpcloud-more-menu-wrapper'
				) }
			>
					<div
						className="wpcloud-more-menu__button"
						onClick={ () => {
							setAttributes( {
								showMenu: true,
							} );
						} }
					>
					<Icon icon={icons[icon]} size={iconSize} />
					</div>
					<div
						{ ...innerBlocksProps }
						className={ classNames(
							innerBlocksProps.className,
							className,
							'wpcloud-block-more-menu__content',
							{
								'hide-menu': ! showMenu,
							}
						)}
					style={positionCss}
					/>
			</div>
		</>
	);
}
