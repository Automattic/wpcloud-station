import React from 'react';

export const useBlockProps = jest.fn().mockReturnValue({
  className: 'wp-block-wpcloud-site-detail',
});

export const RichText = ({
  value,
  onChange,
  tagName,
  className,
  placeholder,
}) => (
  <div
    data-testid="rich-text"
    data-tag={tagName}
    className={className}
  >
    <input
      data-testid="rich-text-input"
      value={value}
      onChange={(e) => onChange(e.target.value)}
      placeholder={placeholder}
    />
  </div>
);

RichText.Content = ({ value, tagName, className }) => (
  <div
    data-testid="rich-text-content"
    data-tag={tagName}
    className={className}
  >
    {value}
  </div>
);

export const InspectorControls = ({ children }) => (
  <div data-testid="inspector-controls">{children}</div>
);

export default {
  useBlockProps,
  RichText,
  InspectorControls,
};
