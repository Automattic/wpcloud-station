/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __, _x, sprintf } from '@wordpress/i18n';
import {
	InspectorControls,
	RichText,
	useBlockProps,
	__experimentalUseBorderProps as useBorderProps, // eslint-disable-line @wordpress/no-unsafe-wp-apis
	__experimentalUseColorProps as useColorProps, // eslint-disable-line @wordpress/no-unsafe-wp-apis
} from '@wordpress/block-editor';
import { PanelBody, CheckboxControl, SelectControl } from '@wordpress/components';
import { useRef } from '@wordpress/element';

/**
 *
 * Internal dependencies
 */
// @TODO: We should pick this up from PHP WPCLOUD_Site::WPCLOUD_SITE_DETAIL_KEYS
const siteDetailKeys = [
	'',
	'domain_name',
	'server_pool_id',
	'atomic_client_id',
	'chroot_path',
	'chroot_ssh_path',
	'cache_prefix',
	'db_charset',
	'db_collate',
	'db_password',
	'php_version',
	'site_api_key',
	'wp_admin_email',
	'wp_admin_user',
	'wp_version',
	'static_file_404',
	'smtp_pass',
	'geo_affinity',
	'ip_addresses',
];

const getDisplayKey = (key = '') => {
	return key.replace(/_/g, ' ')
		.replace(/\b\w/g, s => s.toUpperCase())
		.replace(/\b(.{2})\b/i, twochar => twochar.toUpperCase())
		.replace(/\bapi\b/i, 'API')
		.replace(/\bphp\b/i, 'PHP')
};

function SiteDetailBlock( { attributes, setAttributes, className } ) {
	const { detailLabel, key, adminOnly, inline, displayKey } = attributes;
	const blockProps = useBlockProps();
	const ref = useRef();

	const borderProps = useBorderProps( attributes );
	const colorProps = useColorProps( attributes );
	if ( ref.current ) {
		ref.current.focus();
	}


	const controls = (
		<>
			<InspectorControls>
				<PanelBody title={__('Settings')}>
					<SelectControl
						label={__('Select a site detail')}
						value={key}
						options={siteDetailKeys.map((detailKey) => ({
							value: detailKey,
							label: getDisplayKey(detailKey)
						}))}
						onChange={(newKey) => {
							const display = getDisplayKey(newKey);
							setAttributes({
								key: newKey,
								displayKey: sprintf(_x('{The %s}', 'The display name for the site detail key', 'wpcloud'), display ),
								detailLabel: display,
							})
						}}
					/>
					<CheckboxControl
						label={ __( 'Display Inline' ) }
						checked={ inline }
						onChange={ ( newVal ) => {
							setAttributes( { inline: newVal } );
						} }
					/>
					<CheckboxControl
						label={ __( 'Limit to Admins' ) }
						checked={ adminOnly }
						onChange={ ( newVal ) => {
							setAttributes( {
								adminOnly: newVal,
							} );
						} }
						help={ __(
							'Only admins will see this field. Inputs marked as admin only will appear with a dashed border in the editor'
						) }
					/>
				</PanelBody>
			</InspectorControls>
		</>
	);

	return (
		<div {...blockProps}
			className={classNames( className, 'wpcloud-block-site-detail', colorProps.className, borderProps.className)}
			>
			{ controls }
			<span
				className={ classNames( 'wpcloud-block-site-detail__label', {
					'is-inline': inline,
					'is-admin-only': adminOnly,
				} ) }
			>
				<RichText
					tagName="span"
					className="wpcloud-block-site-detail__label-content"
					value={ detailLabel }
					onChange={ ( newDetail ) =>
						setAttributes( { detailLabel: newDetail } )
					}
					placeholder={ __( 'Detail' ) }
				/>

				<div
					className={ classNames(
						className,
						'wpcloud-block-site-detail__value-placeholder',
					)}
				 	>{ displayKey }	</div>
			</span>
		</div>
	);
}

export default SiteDetailBlock;
