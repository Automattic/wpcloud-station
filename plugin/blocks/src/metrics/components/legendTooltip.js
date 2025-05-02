import { useLegendTooltip } from './legendTooltipContext';

export const LegendTooltip = () => {
	const { tooltip, cancelHide, hide } = useLegendTooltip();

	if (!tooltip.visible) return null;

	return (
		<div
			style={{
				position: 'fixed',
				top: tooltip.y,
				left: tooltip.x,
				pointerEvents: 'none',
				zIndex: 1000,
				whiteSpace: 'nowrap',
			}}
			className="wpcloud-graph-tooltip wpcloud-graph-tooltip--legend"
			onMouseEnter={cancelHide}
			onMouseLeave={() => hide()}
		>
			{tooltip.text}
		</div>
	);
};