/**
 * External dependencies
 */
import { render, screen, fireEvent } from '@testing-library/react';
import userEvent from '@testing-library/user-event';

/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import Edit from '../../../../../blocks/src/components/site-detail/edit';
import save from '../../../../../blocks/src/components/site-detail/save';
import metadata from '../../../../../blocks/src/components/site-detail/block.json';

// Mock the WordPress dependencies
jest.mock('@wordpress/block-editor', () => ({
    useBlockProps: jest.fn().mockReturnValue({ className: 'wp-block-wpcloud-site-detail' }),
    useBlockProps: {
        save: jest.fn().mockReturnValue({ className: 'wp-block-wpcloud-site-detail' }),
    },
    RichText: ({ value, onChange, tagName, className, placeholder }) => (
        <div data-testid="rich-text" data-tag={tagName} className={className}>
            <input
                data-testid="rich-text-input"
                value={value}
                onChange={(e) => onChange(e.target.value)}
                placeholder={placeholder}
            />
        </div>
    ),
    RichText: {
        Content: ({ value, tagName, className }) => (
            <div data-testid="rich-text-content" data-tag={tagName} className={className}>
                {value}
            </div>
        ),
    },
    InspectorControls: ({ children }) => <div data-testid="inspector-controls">{children}</div>,
}));

jest.mock('@wordpress/components', () => ({
    PanelBody: ({ children, label }) => (
        <div data-testid="panel-body" data-label={label}>
            {children}
        </div>
    ),
    ToggleControl: ({ label, checked, onChange, help }) => (
        <div data-testid="toggle-control" data-label={label}>
            <label>{label}</label>
            <input
                type="checkbox"
                checked={checked}
                onChange={() => onChange(!checked)}
                data-testid={`toggle-${label.toLowerCase().replace(/\s+/g, '-')}`}
            />
            {help && <p>{help}</p>}
        </div>
    ),
}));

jest.mock('@wordpress/icons', () => ({
    Icon: ({ icon, className }) => (
        <div data-testid="icon" data-icon={icon.name} className={className}></div>
    ),
    copySmall: { name: 'copy-small' },
    seen: { name: 'seen' },
}));

jest.mock('@wordpress/i18n', () => ({
    __: (text) => text,
}));

// Mock the DetailSelectControl component
jest.mock('@wpcloud/controls/site', () => ({
    DetailSelectControl: ({ attributes, setAttributes }) => (
        <div data-testid="detail-select-control">
            <select
                data-testid="detail-select"
                value={attributes.name}
                onChange={(e) => setAttributes({ name: e.target.value })}
            >
                <option value="">Select a detail</option>
                <option value="site_url">Site URL</option>
                <option value="domain_name">Domain Name</option>
                <option value="wp_version">WordPress Version</option>
                <option value="ssh_user">SSH User</option>
            </select>
        </div>
    ),
}));

describe('wpcloud/site-detail block', () => {
    beforeAll(() => {
        // Register the block
        registerBlockType(metadata.name, {
            edit: Edit,
            save,
        });
    });

    describe('Edit component', () => {
        it('renders correctly with default attributes', () => {
            const attributes = {
                label: 'Test Label',
                name: '',
                adminOnly: false,
                inline: false,
                hideLabel: false,
                showCopyButton: false,
                obscureValue: false,
                revealButton: true,
            };
            const setAttributes = jest.fn();

            render(<Edit attributes={attributes} setAttributes={setAttributes} />);

            // Check that the component renders with the correct label
            expect(screen.getByTestId('rich-text-input')).toHaveValue('Test Label');

            // Check that the value is displayed with the placeholder format
            expect(screen.getByText('{ Test Label }')).toBeInTheDocument();

            // Check that the inspector controls are rendered
            expect(screen.getByTestId('inspector-controls')).toBeInTheDocument();

            // Check that the detail select control is rendered
            expect(screen.getByTestId('detail-select-control')).toBeInTheDocument();
        });

        it('updates label when changed', () => {
            const attributes = {
                label: 'Test Label',
                name: '',
                adminOnly: false,
                inline: false,
                hideLabel: false,
                showCopyButton: false,
                obscureValue: false,
                revealButton: true,
            };
            const setAttributes = jest.fn();

            render(<Edit attributes={attributes} setAttributes={setAttributes} />);

            // Change the label
            const input = screen.getByTestId('rich-text-input');
            fireEvent.change(input, { target: { value: 'New Label' } });

            // Check that setAttributes was called with the new label
            expect(setAttributes).toHaveBeenCalledWith({
                label: 'New Label',
                metadata: { name: 'New Label' },
            });
        });

        it('toggles inline display', () => {
            const attributes = {
                label: 'Test Label',
                name: '',
                adminOnly: false,
                inline: false,
                hideLabel: false,
                showCopyButton: false,
                obscureValue: false,
                revealButton: true,
            };
            const setAttributes = jest.fn();

            render(<Edit attributes={attributes} setAttributes={setAttributes} />);

            // Toggle the inline display
            const toggle = screen.getByTestId('toggle-display-inline');
            fireEvent.click(toggle);

            // Check that setAttributes was called with the new inline value
            expect(setAttributes).toHaveBeenCalledWith({ inline: true });
        });

        it('toggles hide label', () => {
            const attributes = {
                label: 'Test Label',
                name: '',
                adminOnly: false,
                inline: false,
                hideLabel: false,
                showCopyButton: false,
                obscureValue: false,
                revealButton: true,
            };
            const setAttributes = jest.fn();

            render(<Edit attributes={attributes} setAttributes={setAttributes} />);

            // Toggle the hide label
            const toggle = screen.getByTestId('toggle-show-value-only');
            fireEvent.click(toggle);

            // Check that setAttributes was called with the new hideLabel value
            expect(setAttributes).toHaveBeenCalledWith({ hideLabel: true });
        });

        it('toggles show copy button', () => {
            const attributes = {
                label: 'Test Label',
                name: '',
                adminOnly: false,
                inline: false,
                hideLabel: false,
                showCopyButton: false,
                obscureValue: false,
                revealButton: true,
            };
            const setAttributes = jest.fn();

            render(<Edit attributes={attributes} setAttributes={setAttributes} />);

            // Toggle the show copy button
            const toggle = screen.getByTestId('toggle-add-copy-to-clipboard-button');
            fireEvent.click(toggle);

            // Check that setAttributes was called with the new showCopyButton value
            expect(setAttributes).toHaveBeenCalledWith({ showCopyButton: true });
        });

        it('toggles obscure value', () => {
            const attributes = {
                label: 'Test Label',
                name: '',
                adminOnly: false,
                inline: false,
                hideLabel: false,
                showCopyButton: false,
                obscureValue: false,
                revealButton: true,
            };
            const setAttributes = jest.fn();

            render(<Edit attributes={attributes} setAttributes={setAttributes} />);

            // Toggle the obscure value
            const toggle = screen.getByTestId('toggle-obscure-value');
            fireEvent.click(toggle);

            // Check that setAttributes was called with the new obscureValue value
            expect(setAttributes).toHaveBeenCalledWith({ obscureValue: true });
        });

        it('toggles admin only', () => {
            const attributes = {
                label: 'Test Label',
                name: '',
                adminOnly: false,
                inline: false,
                hideLabel: false,
                showCopyButton: false,
                obscureValue: false,
                revealButton: true,
            };
            const setAttributes = jest.fn();

            render(<Edit attributes={attributes} setAttributes={setAttributes} />);

            // Toggle the admin only
            const toggle = screen.getByTestId('toggle-limit-to-admins');
            fireEvent.click(toggle);

            // Check that setAttributes was called with the new adminOnly value
            expect(setAttributes).toHaveBeenCalledWith({ adminOnly: true });
        });

        it('shows reveal button toggle when obscure value is true', () => {
            const attributes = {
                label: 'Test Label',
                name: '',
                adminOnly: false,
                inline: false,
                hideLabel: false,
                showCopyButton: false,
                obscureValue: true,
                revealButton: true,
            };
            const setAttributes = jest.fn();

            render(<Edit attributes={attributes} setAttributes={setAttributes} />);

            // Check that the reveal button toggle is rendered
            expect(screen.getByTestId('toggle-add-reveal-button')).toBeInTheDocument();
        });

        it('does not show reveal button toggle when obscure value is false', () => {
            const attributes = {
                label: 'Test Label',
                name: '',
                adminOnly: false,
                inline: false,
                hideLabel: false,
                showCopyButton: false,
                obscureValue: false,
                revealButton: true,
            };
            const setAttributes = jest.fn();

            render(<Edit attributes={attributes} setAttributes={setAttributes} />);

            // Check that the reveal button toggle is not rendered
            expect(screen.queryByTestId('toggle-add-reveal-button')).not.toBeInTheDocument();
        });

        it('displays obscured value when obscureValue is true', () => {
            const attributes = {
                label: 'Test Label',
                name: '',
                adminOnly: false,
                inline: false,
                hideLabel: false,
                showCopyButton: false,
                obscureValue: true,
                revealButton: true,
            };
            const setAttributes = jest.fn();

            render(<Edit attributes={attributes} setAttributes={setAttributes} />);

            // Check that the value is displayed as asterisks
            expect(screen.getByText('{ ******** }')).toBeInTheDocument();
        });

        it('displays copy button when showCopyButton is true', () => {
            const attributes = {
                label: 'Test Label',
                name: '',
                adminOnly: false,
                inline: false,
                hideLabel: false,
                showCopyButton: true,
                obscureValue: false,
                revealButton: true,
            };
            const setAttributes = jest.fn();

            render(<Edit attributes={attributes} setAttributes={setAttributes} />);

            // Check that the copy button is rendered
            expect(screen.getByTestId('icon')).toHaveAttribute('data-icon', 'copy-small');
        });

        it('displays reveal button when obscureValue and revealButton are true', () => {
            const attributes = {
                label: 'Test Label',
                name: '',
                adminOnly: false,
                inline: false,
                hideLabel: false,
                showCopyButton: false,
                obscureValue: true,
                revealButton: true,
            };
            const setAttributes = jest.fn();

            render(<Edit attributes={attributes} setAttributes={setAttributes} />);

            // Check that the reveal button is rendered
            expect(screen.getByTestId('icon')).toHaveAttribute('data-icon', 'seen');
        });

        it('does not display label when hideLabel is true', () => {
            const attributes = {
                label: 'Test Label',
                name: '',
                adminOnly: false,
                inline: false,
                hideLabel: true,
                showCopyButton: false,
                obscureValue: false,
                revealButton: true,
            };
            const setAttributes = jest.fn();

            render(<Edit attributes={attributes} setAttributes={setAttributes} />);

            // Check that the label is not rendered
            expect(screen.queryByTestId('rich-text')).not.toBeInTheDocument();
        });
    });

    describe('Save component', () => {
        it('renders correctly with default attributes', () => {
            const attributes = {
                label: 'Test Label',
                name: '',
                adminOnly: false,
                inline: false,
                hideLabel: false,
                showCopyButton: false,
                obscureValue: false,
                revealButton: true,
            };

            render(<save attributes={attributes} />);

            // Check that the component renders with the correct label
            expect(screen.getByTestId('rich-text-content')).toHaveTextContent('Test Label');

            // Check that the value is displayed with the placeholder format
            expect(screen.getByText('{ Test Label }')).toBeInTheDocument();
        });

        it('applies correct classes based on attributes', () => {
            const attributes = {
                label: 'Test Label',
                name: '',
                adminOnly: true,
                inline: true,
                hideLabel: false,
                showCopyButton: false,
                obscureValue: false,
                revealButton: true,
            };

            const { container } = render(<save attributes={attributes} />);

            // Check that the correct classes are applied
            expect(container.firstChild).toHaveClass('is-inline');
            expect(container.firstChild).toHaveClass('is-admin-only');
        });

        it('does not display label when hideLabel is true', () => {
            const attributes = {
                label: 'Test Label',
                name: '',
                adminOnly: false,
                inline: false,
                hideLabel: true,
                showCopyButton: false,
                obscureValue: false,
                revealButton: true,
            };

            render(<save attributes={attributes} />);

            // Check that the label is not rendered
            expect(screen.queryByTestId('rich-text-content')).not.toBeInTheDocument();
        });

        it('applies is-obscured class when obscureValue is true', () => {
            const attributes = {
                label: 'Test Label',
                name: '',
                adminOnly: false,
                inline: false,
                hideLabel: false,
                showCopyButton: false,
                obscureValue: true,
                revealButton: true,
            };

            const { container } = render(<save attributes={attributes} />);

            // Check that the is-obscured class is applied
            expect(container.querySelector('.wpcloud-block-site-detail__value-container')).toHaveClass('is-obscured');
        });

        it('displays copy button when showCopyButton is true', () => {
            const attributes = {
                label: 'Test Label',
                name: '',
                adminOnly: false,
                inline: false,
                hideLabel: false,
                showCopyButton: true,
                obscureValue: false,
                revealButton: true,
            };

            render(<save attributes={attributes} />);

            // Check that the copy button is rendered
            expect(screen.getByTestId('icon')).toHaveAttribute('data-icon', 'copy-small');
        });

        it('displays reveal button when obscureValue and revealButton are true', () => {
            const attributes = {
                label: 'Test Label',
                name: '',
                adminOnly: false,
                inline: false,
                hideLabel: false,
                showCopyButton: false,
                obscureValue: true,
                revealButton: true,
            };

            render(<save attributes={attributes} />);

            // Check that the reveal button is rendered
            expect(screen.getByTestId('icon')).toHaveAttribute('data-icon', 'seen');
        });
    });
});

// Test the view.js functionality
describe('site-detail view.js', () => {
    beforeEach(() => {
        // Set up the DOM
        document.body.innerHTML = `
            <div class="wpcloud-block-site-detail">
                <div class="wpcloud-block-site-detail__wrapper" data-site-detail="site_url">
                    <div class="wpcloud-block-site-detail__value">example.com</div>
                    <div class="wpcloud-copy-to-clipboard"></div>
                </div>
            </div>
            <div class="wpcloud-block-site-detail">
                <div class="wpcloud-block-site-detail__wrapper" data-site-detail="ssh_user" data-clipboard-pattern="ssh -v {ssh_user}@ssh.atomicsites.net">
                    <div class="wpcloud-block-site-detail__value">user123</div>
                    <div class="wpcloud-copy-to-clipboard"></div>
                </div>
            </div>
            <div class="wpcloud-block-site-detail">
                <div class="wpcloud-block-site-detail__wrapper" data-site-detail="password">
                    <div class="wpcloud-block-site-detail__value-container is-obscured">
                        <div class="wpcloud-block-site-detail__value">password123</div>
                        <div class="wpcloud-reveal-value"></div>
                    </div>
                </div>
            </div>
            <div class="wpcloud-block-site-detail">
                <div class="wpcloud-block-site-detail__wrapper" data-site-detail="tags">
                    <div class="wpcloud-block-site-detail__value">
                        <ul class="wpcloud_block_site_detail__value__list">
                            <li>tag1</li>
                            <li>tag2</li>
                            <li>tag3</li>
                        </ul>
                    </div>
                    <div class="wpcloud-copy-to-clipboard"></div>
                </div>
            </div>
        `;

        // Mock the clipboard API
        Object.defineProperty(navigator, 'clipboard', {
            value: {
                writeText: jest.fn().mockResolvedValue(undefined),
            },
            configurable: true,
        });

        // Mock the window.wpcloud object
        window.wpcloud = {};

        // Import the view.js file
        require('../../../../../blocks/src/components/site-detail/view.js');
    });

    it('copies simple value to clipboard when copy button is clicked', async () => {
        // Click the copy button
        const copyButton = document.querySelector('.wpcloud-block-site-detail__wrapper[data-site-detail="site_url"] .wpcloud-copy-to-clipboard');
        copyButton.click();

        // Check that the clipboard API was called with the correct value
        expect(navigator.clipboard.writeText).toHaveBeenCalledWith('example.com');
    });

    it('copies value with pattern to clipboard when copy button is clicked', async () => {
        // Click the copy button
        const copyButton = document.querySelector('.wpcloud-block-site-detail__wrapper[data-site-detail="ssh_user"] .wpcloud-copy-to-clipboard');
        copyButton.click();

        // Check that the clipboard API was called with the correct value
        expect(navigator.clipboard.writeText).toHaveBeenCalledWith('ssh -v user123@ssh.atomicsites.net');
    });

    it('does not copy obscured value to clipboard when copy button is clicked', async () => {
        // Click the copy button (there isn't one in this case, but we'll simulate it)
        const detail = document.querySelector('.wpcloud-block-site-detail__wrapper[data-site-detail="password"]');
        const event = { currentTarget: { closest: () => detail } };
        window.wpcloud.copyToClipboard(event);

        // Check that the clipboard API was not called
        expect(navigator.clipboard.writeText).not.toHaveBeenCalled();
    });

    it('copies list values to clipboard when copy button is clicked', async () => {
        // Click the copy button
        const copyButton = document.querySelector('.wpcloud-block-site-detail__wrapper[data-site-detail="tags"] .wpcloud-copy-to-clipboard');
        copyButton.click();

        // Check that the clipboard API was called with the correct value
        expect(navigator.clipboard.writeText).toHaveBeenCalledWith('tag1\ntag2\ntag3\n');
    });

    it('reveals obscured value when reveal button is clicked', () => {
        // Click the reveal button
        const revealButton = document.querySelector('.wpcloud-reveal-value');
        revealButton.click();

        // Check that the is-obscured class was toggled
        expect(document.querySelector('.is-obscured')).toBeNull();
    });
});
