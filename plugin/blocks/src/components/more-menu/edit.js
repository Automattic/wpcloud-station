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
	InnerBlocks
} from '@wordpress/block-editor';
import { ToggleControl, PanelBody, Button } from '@wordpress/components';
import { Icon, moreVertical } from '@wordpress/icons'



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
export default function Edit({ attributes, setAttributes, className }) {
	const { adminOnly, showMenu } = attributes;
	const blockProps = useBlockProps();
	const innerBlocksProps = useInnerBlocksProps(blockProps, {
		renderAppender: InnerBlocks.ButtonBlockAppender,
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
				<ToggleControl
					label={__('Limit to Admins')}
					checked={adminOnly}
					onChange={(newVal) => {
						setAttributes({
							adminOnly: newVal,
						});
					}}
					help={__(
						'Only admins will see this field. Inputs marked as admin only will appear with a dashed border in the editor'
					)}
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
						{
							'is-admin-only': adminOnly,
						}
					)}
					/>)}
			</div>
		</>
	);
}
