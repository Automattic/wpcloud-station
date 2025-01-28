/**
 * WordPress dependencies
 */
import { cloneElement } from '@wordpress/element';

function Spinner({ spinner, size, ...props }, ref) {
	const height = size || '100%';
	const width = size || '100%';
	const Spin = spinner;
	return cloneElement(<Spin />, { width, height, ...props }, ref);
}

export default Spinner;