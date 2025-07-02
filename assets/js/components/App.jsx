import React, { useState } from 'react';
import aistrix from '/img/aistrix.jpg';
import TabButton from './TabButton';

export default function App() {
  // Ruta base del plugin
  const pluginBase = M.cfg.wwwroot + '/local/aistrix/amd/build/';

  // Estado para controlar la visibilidad del botón
  const [visible, setVisible] = useState(false);
  const [loading, setLoading] = useState(false);
  const [result, setResult] = useState(null);
  const [error, setError] = useState(null);

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

  

  return (
    <div style={{ position: 'relative' }}>
      <TabButton visible={visible} setVisible={setVisible} pluginBase={pluginBase} />
      {visible && (
        <div
          style={{
            position: 'fixed',
            top: 0,
            right: 0,
            width: '300px',
            height: '100vh',
            background: '#fff',
            borderLeft: '1px solid #ccc',
            boxShadow: '-2px 0 8px rgba(0,0,0,0.1)',
            padding: '1rem',
            zIndex: 9999,
          }}
        >
          <h2>Aistrix</h2>
          <p>Asistente React en Moodle</p>
          <button onClick={() => enviarVPL()} disabled={loading} style={{marginBottom: '1rem'}}>
            {loading ? 'Enviando...' : 'Enviar datos VPL'}
          </button>
          {result && <div style={{color: 'green', marginBottom: '1rem'}}>{result}</div>}
          {error && <div style={{color: 'red', marginBottom: '1rem'}}>{error}</div>}
        </div>
      )}
    </div>
  );
}
