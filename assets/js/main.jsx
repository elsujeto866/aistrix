/**
 * Punto de entrada principal de la aplicación React Aistrix
 * 
 * Este archivo se encarga de:
 * - Inicializar la aplicación React en el DOM
 * - Importar estilos SCSS compilados
 * - Exportar función init() para el sistema AMD de Moodle
 * 
 * La función init() es llamada automáticamente por Moodle cuando
 * se carga la página view.php a través de:
 * $PAGE->requires->js_call_amd('local_aistrix/main', 'init');
 * 
 * @package    local_aistrix
 * @copyright  2025 EPN
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './components/App';
import '@/scss/style.scss';

/**
 * Inicializa la aplicación React en el contenedor DOM
 * 
 * Esta función es llamada por el sistema AMD de Moodle y monta
 * la aplicación React en el elemento con ID 'aistrix-root'
 */
export function init() {
    const container = document.getElementById('aistrix-root');
    if (container) {
        const root = createRoot(container);
        root.render(React.createElement(App));
    }
}

