import { createRoot } from 'react-dom/client';
const container = document.getElementById('app');
const root = createRoot(container); // createRoot(container!) if you use TypeScript
root.render(<App tab="home" />);

 document.addEventListener('DOMContentLoaded', () => {
       const domNode = document.getElementById('my-countdown');
       ReactDOM.render(<MY_Countdown />, domNode);
});