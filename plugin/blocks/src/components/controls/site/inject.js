
/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';


/**
 * Internal dependencies
 */
import DetailSelectControl from './site/detailSelect';


/**
 * Inject a 'WP Cloud Site' control section for the site block.

 */

wp.compose.createHigherOrderComponent((BlockEdit) => {
	return ({ attributes, setAttributes, isSelected, }) => {
		return (
			<>
				<BlockEdit {...props} />
				{isSelected && (props.name == 'wpcloud/site') &&
					<InspectorControls>
						<PanelBody title={__('WP Cloud Site')}>
							{ props.name == 'wpcloud/form' && <DetailSelectControl attributes={attributes} setAttributes={setAttributes} /> }
							</PanelBody>
					</InspectorControls>
				}
			</>
		);
	};
}, 'wpcloudSiteControls');

wp.hooks.addFilter(
	'editor.BlockEdit',
	'wpcloud/site-controls',
	SiteDetailSelectControl
);