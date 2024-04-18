/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';


const Edit = ({ attributes }) => {
	const { text } = attributes;
	const template = [
		[ 'core/button',
			{
				text: text || __('Submit'),
				tagName: 'button',
				type: 'submit',
			},
		],
	];

	const blockProps = useBlockProps();
	const innerBlocksProps = useInnerBlocksProps(blockProps, {
		template: template,
	} );
	return (
		<div className="wpcloud-block-form-submit-wrapper" { ...innerBlocksProps } />
	);
};
export default Edit;
