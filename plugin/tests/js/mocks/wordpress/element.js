import React from 'react';

export const useEffect = jest.fn((callback, deps) => {
  callback();
  return undefined;
});

export const useState = jest.fn((initialState) => [initialState, jest.fn()]);

export const useRef = jest.fn((initialValue) => ({ current: initialValue }));

export const Fragment = React.Fragment;

export default {
  useEffect,
  useState,
  useRef,
  Fragment,
};
