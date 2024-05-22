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
import { ToggleControl, PanelBody } from '@wordpress/components';
import { Icon, moreVertical } from '@wordpress/icons'
import { useSelect } from '@wordpress/data';


/**
 * Internal dependencies
 */
import './editor.scss';

/**
 *
 * @param {Object} props               Component props.
 * @param {Object} props.attributes
 * @param {Object} props.setAttributes
 * @return {Element} Element to render.
 */
export default function Edit({ attributes, setAttributes, className, clientId }) {
	const { showMenu } = attributes;
	const blockProps = useBlockProps();

	const isChildSelected = useSelect( ( select ) =>
			select( 'core/block-editor' ).hasSelectedInnerBlock( clientId )
	);
	const innerBlocksProps = useInnerBlocksProps(blockProps, {
		renderAppender: isChildSelected ? undefined : InnerBlocks.ButtonBlockAppender,
	});

	const controls = (
		<InspectorControls>
			<PanelBody title={__('Form Settings')}>
				<ToggleControl
					label={__('Show Menu')}
					checked={showMenu}
					onChange={(newVal) => {
						setAttributes({
							showMenu: newVal,
						});
					}}
				/>
			</PanelBody>
		</InspectorControls>
	);

	return (
		<>
			{controls}
			<div className={classNames( className, 'wpcloud-more-menu-wrapper' )}>
			{ ! showMenu && (
					<button className="wpcloud-more-menu__button" onClick={() => {
						setAttributes({
							showMenu: true,
						});

					}}><Icon icon={moreVertical} /></button>
			)}
			{showMenu && (
				<div
					{...innerBlocksProps}
					className={ classNames(
						innerBlocksProps.className,
						className,
						'wpcloud-block-more-menu',
					)}
					/>)}
			</div>
		</>
	);
}
