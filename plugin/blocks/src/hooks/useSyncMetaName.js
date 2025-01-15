import { useEffect } from '@wordpress/element';
import { useDispatch } from '@wordpress/data';

/**
 * Custom hook to sync a block attribute with post meta.
 *
 * @param {string} attributeValue - The value of the block attribute to watch.
 * @param {string} metaKey - The meta key to update.
 */

export default function useSyncMetaName(clientId, newName) {
	const { updateBlockAttributes } = useDispatch('core/block-editor');

	useEffect(() => {
		if ( clientId ) {
			updateBlockAttributes(clientId, { metadata: { name: newName } });
		}
	}, [clientId, newName, updateBlockAttributes]);
};