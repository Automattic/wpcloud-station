/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';
import { Icon, seen, unseen } from '@wordpress/icons';


/**
 * Internal dependencies
 */

import Text from './text';

export default function TextField( {
	attributes,
	className,
	onPlaceholderChange,
} ) {
	const controls = <></>;
	attributes.placeholder = '';

	return (
		<>
			{ controls }
			<span className="wpcloud-block-form-input--password">
				<Text
					attributes={attributes}
					className={className}
					onPlaceholderChange={onPlaceholderChange}
				/>
				<span className="wpcloud-block-form-input--toggle-hidden">
					<Icon icon={seen} size={16} className="wpcloud-block-form-input--toggle-hidden--seen" />
					<Icon icon={unseen} size={16} style={{display: "none"}}  className="wpcloud-block-form-input--toggle-hidden--unseen" />
				</span>
			</span>
		</>
	);
}
