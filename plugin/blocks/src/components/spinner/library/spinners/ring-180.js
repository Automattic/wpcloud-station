/**
 * WordPress dependencies
 */
import { SVG, Path } from '@wordpress/primitives';

export default function ({ height = '100%', width = '100%', speed, background }) {
	const dur = 0.75 * speed;
	return (
		<SVG width={width} height={height} viewBox="0 0 24 24" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
			{ background && <Path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25" />}
			<Path d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z">
				<animateTransform attributeName="transform" type="rotate" dur={dur} values="0 12 12;360 12 12" repeatCount="indefinite" />
			</Path>
		</SVG>
	)
}