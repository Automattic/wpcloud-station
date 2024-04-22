
console.log('SiteDetailSelect.js');
const SiteDetailSelectControl = wp.compose.createHigherOrderComponent((BlockEdit) => {

	return (props) => {
		const { Fragment } = wp.element;
		const { ToggleControl } = wp.components;
		const { InspectorAdvancedControls } = wp.blockEditor;
		const { attributes, setAttributes, isSelected } = props;
		return (
			<Fragment>
				<BlockEdit {...props} />
				{isSelected && (props.name == 'wpcloud/input') &&
					<InspectorAdvancedControls>
						<ToggleControl
							label={wp.i18n.__('Hide on mobile', 'awp')}
							checked={!!attributes.hideOnMobile}
							onChange={() => setAttributes({ hideOnMobile: !attributes.hideOnMobile })}
						/>
					</InspectorAdvancedControls>
				}
			</Fragment>
		);
	};
}, 'coverAdvancedControls');

wp.hooks.addFilter(
	'editor.BlockEdit',
	'wpcloud/site-detail-select-control',
	SiteDetailSelectControl
);