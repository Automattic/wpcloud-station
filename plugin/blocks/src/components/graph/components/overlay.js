
/**
 * WordPress dependencies
 */
import { Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default ({ loading, title }) => {
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
