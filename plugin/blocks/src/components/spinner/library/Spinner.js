/**
 * WordPress dependencies
 */
import { cloneElement } from '@wordpress/element';

function Spinner({ spinner, ...props }, ref) {
	console.log(props);
	const Spin = spinner;
	return cloneElement(<Spin />, { ...props }, ref);
}

export default Spinner;