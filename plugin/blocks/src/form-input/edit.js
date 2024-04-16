
import classNames from 'classnames';

import { __ } from '@wordpress/i18n';

import { Text, Select, Hidden } from './fields';

/**
 * @return {Element} Element to render.
 */
export default function Edit(props) {

	const inputs = {
		text: Text,
		select: Select,
		hidden: Hidden,
	};

	const Input = inputs[props.type] || Text;


	return (
		<Input {...props} />
	);
}
