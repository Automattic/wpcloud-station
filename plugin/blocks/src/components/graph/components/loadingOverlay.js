/**
 * WordPress dependencies
 */
import { Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * A loading overlay that shows a spinner on top of the existing graph
 * when data is being refreshed.
 */
export default function LoadingOverlay() {
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
