import React from 'react';

export default function TabButton({visible, setVisible, pluginBase}) {
  return (
    <div>
      <button
        onClick={() => setVisible(!visible)}
        style={{
          position: 'fixed',
          right: visible ? '300px' : '0',
          bottom: '20px',
          zIndex: 10000,
          backgroundColor: '#ffffff',
          color: '#fff',
          borderRadius: '25px 0 0 25px',
          width: '60px',
          height: '60px',
          border: 'none',
          fontSize: '24px',
          padding: 0,
          overflow: 'hidden',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
          boxShadow: '0 2px 8px rgba(0,0,0,0.15)',
          transition: 'right 0.3s'
        }}
      >
        <img
          src={pluginBase + 'aistrix-aistrix.jpg'}
          alt="Búho"
          style={{
            width: '60px',
            height: '60px',
            objectFit: 'cover',
            borderRadius: '50%',
            display: 'block',
            background: '#fff',
            marginLeft: '8px'
          }}
        />
      </button>
    </div>
  );
}