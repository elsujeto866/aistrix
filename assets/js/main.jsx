import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './components/App';
import '../scss/style.scss';

// Crea el div flotante
const container = document.createElement('div');
container.id = 'aistrix-root';
document.body.appendChild(container);

// Monta React
const root = createRoot(container);
root.render(<App />);
