import React from 'react';
import aistrix from '/img/aistrix.jpg';

export default function TabButton({visible, setVisible}) {
  return (
    <div>
      <button className="aistrix-tabbutton" onClick={() => setVisible(!visible)}>
        <img
          src={aistrix}
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