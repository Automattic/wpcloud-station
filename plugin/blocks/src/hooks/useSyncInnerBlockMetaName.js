import { useEffect } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';

export default function useSyncInnerBlockMetaName(clientId, getNameFrom) {
	const { updateBlockAttributes } = useDispatch('core/block-editor');
	const children = useSelect((select) => select('core/block-editor').getBlocks(clientId));
	const newName = getNameFrom(children);

	useEffect(() => {
		if ( clientId && newName ) {
			updateBlockAttributes(clientId, { metadata: { name: newName } });
		}
	}, [clientId, newName, updateBlockAttributes]);

}