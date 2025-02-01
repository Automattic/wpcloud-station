/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps, RichText } from '@wordpress/block-editor';

/** Local Dependencies */
import { Spinner } from '@wpcloud/components/spinner/library';
import * as spinners from '@wpcloud/components/spinner/library';

export default function save( { attributes } ) {
	const { label, iconOnly, addSpinner } = attributes;
	const { spinnerSpinner,
		spinnerBackground: background,
		spinnerSpeed: speed,
		spinnerSize: size } = attributes;
	const spinner = spinners[spinnerSpinner];

	const blockProps = useBlockProps.save();

	return (
		<div
			{ ...blockProps }
			className={'wpcloud-block-button__content'}
			style={ { position: 'relative' } }
		>

			{!iconOnly && (
				<span className={'wpcloud-block-button__label'}>
					<RichText.Content value={label} />
				</span>
			)}
			<InnerBlocks.Content />
			{ addSpinner && (<Spinner className="wpcloud-block-button__spinner visibility-none position-absolute-center" {...{spinner, background, speed, size }} />) }
		</div>
	);
}
