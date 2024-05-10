/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

/**
 * Internal dependencies
 */
import DetailSelectControl from './detailSelect';

/**
 * Inject a 'WP Cloud Site' control section for the site block.
 */

const shouldInjectSiteControls = ( name ) => {
	return name == 'wpcloud/form-input';
};

const SiteControls = wp.compose.createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		const { isSelected, name } = props;
		return (
			<>
				<BlockEdit { ...props } />
				{ isSelected && shouldInjectSiteControls( name ) && (
					<InspectorControls>
						<PanelBody title={ __( 'WP Cloud Site' ) }>
							{ name == 'wpcloud/form-input' && (
								<DetailSelectControl { ...props } />
							) }
						</PanelBody>
					</InspectorControls>
				) }
			</>
		);
	};
}, 'wpcloudSiteControls' );

wp.hooks.addFilter( 'editor.BlockEdit', 'wpcloud/site-controls', SiteControls );
