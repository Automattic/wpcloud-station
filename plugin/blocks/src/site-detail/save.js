/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import {
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';


export default function save({ attributes, className }) {
	const { detailLabel, key } = attributes;
	const blockProps = useBlockProps();
	return (
		<div {...blockProps}
			className={classNames(
				className,
				'wpcloud-block-site-detail',
			) }
		>
			<div className="wpcloud-block-site-detail__label">
				<RichText.Content value={ detailLabel} />
			</div>
			<div className="wpcloud-block-site-detail__value" data-site-detail={ key } />
		</div>
	)
}