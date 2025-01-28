/**
 * WordPress dependencies
 */
import { SVG, G, Rect } from '@wordpress/primitives';

export default function ({ height = '100%', width = '100%', speed, animate = true }) {

	const dur = 0.75 * speed;
	return (
		<SVG width={width} height={height} viewBox="0 0 24 24" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
			<G>
				<Rect x="11" y="1" width="2" height="5" transform="rotate(270 12 12)" opacity="0.1" />
				<Rect x="11" y="1" width="2" height="5" transform="rotate(300 12 12)" opacity="0.2" />
				<Rect x="11" y="1" width="2" height="5" transform="rotate(330 12 12)" opacity="0.3" />
				<Rect x="11" y="1" width="2" height="5" opacity="0.4" />
				<Rect x="11" y="1" width="2" height="5" transform="rotate(30 12 12)" opacity="0.5" />
				<Rect x="11" y="1" width="2" height="5" transform="rotate(60 12 12)" opacity="0.6" />
				<Rect x="11" y="1" width="2" height="5" transform="rotate(90 12 12)" opacity="0.7" />
				<Rect x="11" y="1" width="2" height="5" transform="rotate(120 12 12)" opacity="0.8" />
				<Rect x="11" y="1" width="2" height="5" transform="rotate(150 12 12)" opacity="0.9" />
				<Rect x="11" y="1" width="2" height="5" transform="rotate(180 12 12)" />

				{animate && <animateTransform attributeName="transform" type="rotate" calcMode="discrete" dur={dur} values="0 12 12;30 12 12;60 12 12;90 12 12;120 12 12;150 12 12;180 12 12;210 12 12;240 12 12;270 12 12;300 12 12;330 12 12;360 12 12" repeatCount="indefinite" /> }
			</G>
		</SVG>
	);
}