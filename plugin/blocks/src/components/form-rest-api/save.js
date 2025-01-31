/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const Save = ( { attributes } ) => {
	const blockProps = useBlockProps.save();
	const { inline, endpoint, useStationApi, version, redirect, method } = attributes;

	let op = '';
	switch (method) {
		case 'POST':
			op = '_create';
			break;
		case 'PUT':
			op = '_update';
			break;
		case 'DELETE':
			op = '_delete';
			break;
	}

	const actionEndpoint =
		endpoint
			.replace(/^\//, '')
			.split('/')
			.map( part => part
				.replace(/s$/, '')
				.replace(/ies$/, 'y'))
			.join('_')
	const action = `${actionEndpoint}${op}`;

	return (
		<form
			{...blockProps}
			encType="text/plain"
			className={classNames(
				blockProps.className,
				'wpcloud-block-form',
				{ 'is-inline': inline }
			)}
			data-rest-api-endpoint={endpoint}
			data-use-station-api={useStationApi}
			data-station-rest-api-version={version}
			data-success-redirect={redirect}
			method={method}
			data-wpcloud-action={action}
			action="#"
		>
			<InnerBlocks.Content />
		</form>
	);
};
export default Save;
