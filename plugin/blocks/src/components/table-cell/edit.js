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
} from '@wordpress/block-editor';
import { CheckboxControl, PanelBody } from '@wordpress/components';

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
export default function Edit( { attributes, setAttributes } ) {
	const { isHeader } = attributes;
	const blockProps = useBlockProps();

	const innerBlocksProps = useInnerBlocksProps( blockProps );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Form Settings' ) }>
					<CheckboxControl
						label={ __( 'Header cell' ) }
						checked={ isHeader }
						onChange={ ( newVal ) => {
							setAttributes( {
								isHeader: newVal,
							} );
						} }
						help={ __( 'Enable if the cell is a header cell.' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<span
				{ ...innerBlocksProps }
				className={ classNames(
					innerBlocksProps.className,
					'wpcloud-block-table-cell',
					{
						'is-header': isHeader,
					}
				) }
			/>
		</>
	);
}
