/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	InnerBlocks,
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { ToggleControl, TextControl, PanelBody, RadioControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import {updateAttribute} from '@wpcloud/controls';

/**
 *
 * @param {Object}  props               Component props.
 * @param {Object}  props.attributes
 * @param {Object}  props.setAttributes
 * @param {number}  props.clientId
 * @param {boolean} props.isSelected
 * @return {Element} Element to render.
 */
export default function Edit( {
	attributes,
	setAttributes,
	clientId,
	isSelected,
} ) {
	const { endpoint, useStationApi, inline, redirect, resetOnSuccess, method } = attributes;
	const blockProps = useBlockProps();

	const update = updateAttribute(setAttributes);

	const isChildSelected = useSelect( ( select ) =>
		select( 'core/block-editor' ).hasSelectedInnerBlock( clientId )
	);

	useEffect( () => {
		setAttributes( { active: isSelected || isChildSelected } );
	}, [ isSelected, isChildSelected ] );

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

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		renderappender:
			! hasInnerBlocks || isSelected || isChildSelected
				? InnerBlocks.DefaultBlockAppender
				: undefined,
		template: hasInnerBlocks ? undefined : [ [ 'wpcloud/form-input' ] ],
	});

	const endpointHelp = useStationApi
		? __('WP Cloud Station REST endpoint. Use ${...} to substitute site values. For example ${site.slug} will be replaced with the site slug.')
		: __('Site API endpoint');
	const endpointLabel = useStationApi
		? __('Station Endpoint')
		: __('Endpoint');


	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Form Settings')}>
					<RadioControl
						label={__('Endpoint Type')}
						selected={ useStationApi }
						options={[
							{ label: __('WP Cloud Station'), value: true },
							{ label: __('General'), value: false },
						]}
						onChange={update('userStationApi', option => option === 'true' ) }
					/>
					<TextControl
						label={ endpointLabel }
						value={ endpoint }
						onChange={ update('endpoint') }
						help={ endpointHelp }
					/>
					{/* TODO: add select to choose version. for now we just have v1 endpoints */}
					<RadioControl
						label={__('Method')}
						selected={method}
						options={[
							{ label: __('Create (POST)'), value: 'POST' },
							{ label: __('Update (PUT)'), value: 'Put' },
							{ label: __('Delete (DELETE)'), value: 'DELETE' },
						]}
						onChange={update('method')}
					/>
					<ToggleControl
						label={ __( 'Display Inline' ) }
						checked={ inline }
						onChange={ update('inline') }
					/>
					<ToggleControl
						label={ __( 'Reset on Success' ) }
						checked={ resetOnSuccess }
						onChange={ update('resetOnSuccess') }
						help={ __(
							'Clear the form fields after a successful submission.'
						)}
					/>
					<TextControl
						label={ __( 'Redirect' ) }
						value={ redirect }
						onChange={ update('redirect') }
						help={ __(
							'The url to redirect to after a successful form submission. Use ${...} to substitute site values. For example ${site.slug} will be replaced with the site slug.'
						) }
					/>

				</PanelBody>
			</InspectorControls>
			<form
				{ ...innerBlocksProps }
				className={ classNames(
					innerBlocksProps.className,
					'wpcloud-block-form',
					{
						'is-inline': inline,
					}
				) }
				encType="text/plain"
			/>
		</>
	);
}
