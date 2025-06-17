import React, { useState } from 'react';

export default function App() {
  const [visible, setVisible] = useState(false);

  return (
    <div style={{ position: 'relative' }}>
      <button
        onClick={() => setVisible(!visible)}
        style={{
          position: 'fixed',
          right: '20px',
          bottom: '20px',
          zIndex: 10000,
          backgroundColor: '#0055aa',
          color: '#fff',
          borderRadius: '50%',
          width: '50px',
          height: '50px',
          border: 'none',
          fontSize: '24px',
        }}
      >
        🦉
      </button>

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
        </div>
      )}
    </div>
  );
}
