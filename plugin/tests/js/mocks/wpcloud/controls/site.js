import React from 'react';

export const DetailSelectControl = ({ attributes, setAttributes }) => (
  <div data-testid="detail-select-control">
    <select
      data-testid="detail-select-control-input"
      value={attributes.name}
      onChange={(e) => setAttributes({ name: e.target.value })}
    >
      <option value="name">Name</option>
      <option value="url">URL</option>
      <option value="id">ID</option>
      <option value="status">Status</option>
      <option value="created">Created</option>
    </select>
  </div>
);

export default {
  DetailSelectControl,
};
