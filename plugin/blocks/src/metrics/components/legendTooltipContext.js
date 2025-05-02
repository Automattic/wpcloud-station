import { createContext, useContext, useState, useRef, useCallback } from 'react';

const LegendTooltipContext = createContext(null);

export const LegendTooltipProvider = ({ children }) => {
  const [tooltip, setTooltip] = useState({ visible: false, text: '', x: 0, y: 0 });
  const timeoutRef = useRef();

  const show = useCallback((text, event) => {
    clearTimeout(timeoutRef.current);
    const { clientX, clientY } = event || { clientX: 0, clientY: 0 };
    setTooltip({ visible: true, text, x: clientX + 12, y: clientY + 12 }); // offset by 12px
  }, []);

  const hide = useCallback((delay = 100) => {
    timeoutRef.current = setTimeout(() => {
      setTooltip(t => ({ ...t, visible: false }));
    }, delay);
  }, []);

  const cancelHide = useCallback(() => {
    clearTimeout(timeoutRef.current);
  }, []);

  return (
    <LegendTooltipContext.Provider value={{ tooltip, show, hide, cancelHide }}>
      {children}
    </LegendTooltipContext.Provider>
  );
};

export const useLegendTooltip = () => useContext(LegendTooltipContext);