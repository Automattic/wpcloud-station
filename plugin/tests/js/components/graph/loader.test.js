/**
 * External dependencies
 */
import { render, screen } from '@testing-library/react';

/**
 * Internal dependencies
 */
import Loader from '../../../../blocks/src/components/graph/components/loader';

describe('Loader Component', () => {
    it('renders without crashing', () => {
        render(<Loader />);
        expect(screen.getByTestId('spinner')).toBeInTheDocument();
    });

    it('renders a spinner with correct styling', () => {
        render(<Loader />);
        const spinner = screen.getByTestId('spinner');
        expect(spinner).toBeInTheDocument();
    });

    it('renders a semi-transparent overlay', () => {
        const { container } = render(<Loader />);
        // The first child should be the overlay div
        const overlay = container.firstChild;
        expect(overlay).toHaveStyle({
            width: '100%',
            height: '100%',
            backgroundColor: 'white',
            opacity: '66%',
        });
    });

    it('centers the spinner', () => {
        const { container } = render(<Loader />);
        // The second child should be the spinner container
        const spinnerContainer = container.childNodes[1];
        expect(spinnerContainer).toHaveStyle({
            position: 'absolute',
            top: '50%',
            left: '50%',
            transform: 'translate(-50%, -50%)',
        });
    });
});
