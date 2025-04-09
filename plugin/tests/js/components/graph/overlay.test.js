/**
 * External dependencies
 */
import { render, screen } from '@testing-library/react';

/**
 * Internal dependencies
 */
import Overlay from '../../../../blocks/src/components/graph/components/overlay';

describe('Overlay Component', () => {
    it('renders a spinner with proper accessibility attributes when loading', () => {
        render(<Overlay loading={true} />);
        const statusElement = screen.getByRole('status');
        expect(statusElement).toBeInTheDocument();
        expect(statusElement).toHaveAttribute('aria-label', 'Loading data');
        expect(statusElement).toHaveAttribute('aria-live', 'polite');

        // The Spinner component is inside the status element
        // We don't need to check the specific DOM structure of the Spinner
        // as it's an implementation detail of the @wordpress/components library
        expect(statusElement.firstChild).toBeTruthy();
    });

    it('renders "No data available" message with proper accessibility attributes when not loading', () => {
        render(<Overlay loading={false} title="Test Title" />);
        const alertElement = screen.getByRole('alert');
        expect(alertElement).toBeInTheDocument();
        expect(alertElement).toHaveAttribute('aria-live', 'assertive');

        // Check that the message and title are inside the alert element
        expect(alertElement).toHaveTextContent('No data available');
        expect(alertElement).toHaveTextContent('Test Title');
    });

    it('renders a semi-transparent overlay with proper accessibility attributes', () => {
        const { container } = render(<Overlay loading={true} />);
        // The first child should be the overlay div
        const overlay = container.firstChild;
        expect(overlay).toHaveStyle({
            width: '100%',
            height: '100%',
            backgroundColor: 'white',
            opacity: '66%',
        });

        // Check that the overlay has the correct accessibility attributes
        expect(overlay).toHaveAttribute('role', 'presentation');
        expect(overlay).toHaveAttribute('aria-hidden', 'true');
    });

    it('centers the content', () => {
        const { container } = render(<Overlay loading={true} />);
        // The second child should be the content container
        const contentContainer = container.childNodes[1];
        expect(contentContainer).toHaveStyle({
            position: 'absolute',
            top: '50%',
            left: '50%',
            transform: 'translate(-50%, -50%)',
        });
    });

    it('displays the title when not loading', () => {
        render(<Overlay loading={false} title="Custom Title" />);
        expect(screen.getByText('Custom Title')).toBeInTheDocument();
    });

    it('has centered text alignment for the no-data message', () => {
        render(<Overlay loading={false} title="Test" />);
        const messageContainer = screen.getByText('Test').parentElement;
        expect(messageContainer).toHaveStyle({
            textAlign: 'center',
        });
    });
});
