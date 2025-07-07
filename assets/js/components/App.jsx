import React, { useState, useEffect } from 'react';
import TabButton from './TabButton';
import Panel from './Panel';

export default function App() {
  // Obtener datos del usuario desde el DOM
  const container = document.getElementById('aistrix-root');
  const username = container?.dataset?.username || 'Usuario';
  const fullname = container?.dataset?.fullname || 'Usuario';
  
  // Estado para controlar la visibilidad del panel
  const [visible, setVisible] = useState(false);
  const [tabOpen, setTabOpen] = useState(false); 
  const [loading, setLoading] = useState(false);
  const [result, setResult] = useState(null);
  const [error, setError] = useState(null);

  // Sincroniza el estado del tab con el panel
  useEffect(() => {
    if (visible) {
      setTabOpen(true);
    } else {
      // Espera la duración de la transición del panel (ej: 300ms)
      const timeout = setTimeout(() => setTabOpen(false), 100);
      return () => clearTimeout(timeout);
    }
  }, [visible]);

  // Atajo de teclado Ctrl+A o Cmd+A para abrir/cerrar el panel
  useEffect(() => {
    function handleKeyDown(e) {
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'a') {
        e.preventDefault();
        setVisible(v => !v);
      }
    }
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, []);

  async function enviarVPL(courseid = null) {
    setLoading(true);
    setResult(null);
    setError(null);
    try {
      const params = {
        methodname: 'local_aistrix_process_vpl',
        args: { courseid }
      };
      const response = await fetch(M.cfg.wwwroot + '/lib/ajax/service.php?sesskey=' + M.cfg.sesskey, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify([params])
      });
      const data = await response.json();
      if (data[0].error) {
        setError(data[0].exception.message);
      } else {
        setResult(data[0].data.message);
      }
    } catch (e) {
      setError('Error de red o inesperado');
    } finally {
      setLoading(false);
    }
  }

  function handleClose() {
    setVisible(false);
    setResult(null);
    setError(null);
  }

  return (
    <div className="aistrix-app">
      <TabButton visible={tabOpen} setVisible={setVisible} />
      <Panel 
        visible={visible}
        onClose={handleClose}
        loading={loading}
        result={result}
        error={error}
        onSendVPL={() => enviarVPL()}
        username={username}
        fullname={fullname}
      />
    </div>
  );
}
