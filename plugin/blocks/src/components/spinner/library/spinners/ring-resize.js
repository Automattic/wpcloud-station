/**
 * WordPress dependencies
 */
import { SVG, G, Circle, Path } from '@wordpress/primitives';


export default function ({ height = '100%', width = '100%', speed, background }) {
	const dur = 2 * speed;
	const innerDur = dur * 0.75;
	return (
		<SVG width={width} height={height} stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
			<G>
				{ background && <Path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25" />}
				<Circle cx="12" cy="12" r="9.5" fill="none" strokeWidth="3" strokeLinecap="round">
					<animate attributeName="stroke-dasharray" dur={`${innerDur}s`} calcMode="spline" values="0 150;42 150;42 150;42 150" keyTimes="0;0.475;0.95;1" keySplines="0.42,0,0.58,1;0.42,0,0.58,1;0.42,0,0.58,1" repeatCount="indefinite" />
					<animate attributeName="stroke-dashoffset" dur={`${innerDur}s`} calcMode="spline" values="0;-16;-59;-59" keyTimes="0;0.475;0.95;1" keySplines="0.42,0,0.58,1;0.42,0,0.58,1;0.42,0,0.58,1" repeatCount="indefinite" />
				</Circle>
				<animateTransform attributeName="transform" type="rotate" dur={`${dur}s`} values="0 12 12;360 12 12" repeatCount="indefinite" />
			</G>
		</SVG>
	);
}