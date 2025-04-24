
/**
 * WordPress dependencies
 */
import { Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Enhanced Overlay component that can handle both initial loading and refreshing states
 *
 * @param {Object} props Component props
 * @param {boolean} props.loading Whether data is loading initially
 * @param {boolean} props.refreshing Whether data is being refreshed
 * @param {string} props.title Graph title for no data message
 * @returns {JSX.Element} Overlay component
 */
export default ({ loading, refreshing = false, title }) => {
	// If refreshing, show a semi-transparent overlay with just the spinner
	if (refreshing) {
		return (
			<div
				style={{
					position: 'absolute',
					top: 0,
					left: 0,
					right: 0,
					bottom: 0,
					display: 'flex',
					alignItems: 'center',
					justifyContent: 'center',
					backgroundColor: 'rgba(255, 255, 255, 0.5)',
					zIndex: 10
				}}
			>
				<div
					role="status"
					aria-label={__('Refreshing data', 'wpcloud')}
					aria-live="polite"
				>
					<Spinner style={{
						height: 'calc(4px * 20)',
						width: 'calc(4px * 20)'
					}} />
				</div>
			</div>
		);
	}

	// For initial loading or no data, show the full overlay
	return (
		<>
			<div
				role="presentation"
				aria-hidden="true"
				style={{ width: "100%", height: "100%", backgroundColor: "white", opacity: "66%" }}
			/>
			<div style={{ position: "absolute", top: "50%", left: "50%", transform: "translate(-50%, -50%)" }}>
				{loading && (
					<div
						role="status"
						aria-label={__('Loading data', 'wpcloud')}
						aria-live="polite"
					>
						<Spinner style={{
							height: 'calc(4px * 20)',
							width: 'calc(4px * 20)'
						}}
						/>
					</div>
				)}
				{!loading && (
					<div
						role="alert"
						aria-live="assertive"
						style={{ textAlign: "center" }}
					>
						<h3>{title}</h3>
						<p style={{ fontSize: "20px" }} >
							{__('No data available', 'wpcloud')}
						</p>
					</div>
				)}
			</div>
		</>
	);
}
