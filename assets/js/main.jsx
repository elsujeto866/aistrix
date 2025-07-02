import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './components/App';
import '../scss/style.scss';

// Usa el div existente en el HTML
const container = document.getElementById('aistrix-root');
if (container) {
  const root = createRoot(container);
  root.render(<App />);
}
