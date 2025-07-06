import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './components/App';

// Módulo AMD que exporta la función init
export function init() {
    const container = document.getElementById('aistrix-root');
    if (container) {
        const root = createRoot(container);
        root.render(React.createElement(App));
    }
}

