import { useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import {
	InnerBlocks,
	store as blockEditorStore
} from '@wordpress/block-editor';

export default function useInnerBlocksInserter({ isSelected, setAttributes }, innerBlocksProps = {}) {
	const clientId = innerBlocksProps['data-block'];
	if ( !clientId ) {
		return innerBlocksProps;
	}

	const isChildSelected = useSelect((select) =>
		select('core/block-editor').hasSelectedInnerBlock(clientId)
	);

	useEffect( () => {
		setAttributes( { active: isSelected || isChildSelected } );
	}, [isSelected, isChildSelected ] );

	const { hasInnerBlocks } = useSelect(
		( select ) => {
			const { getBlock } = select( blockEditorStore );
			const block = getBlock( clientId );
			return {
				hasInnerBlocks: !! ( block && block.innerBlocks.length ),
			};
		},
		[ clientId ]
	);

	if ( ! hasInnerBlocks || isSelected || isChildSelected ) {
		innerBlocksProps.renderAppender = InnerBlocks.DefaultBlockAppender;
	}

	return innerBlocksProps;
}
