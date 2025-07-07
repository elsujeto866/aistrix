import React, {useState} from 'react';
import aistrix from '@/img/aistrix.jpg';

export default function TabButton({visible, setVisible}) {
  const [hovered, setHovered] = useState(false);
  return (
    <button 
    className={`aistrix-tabbutton${visible ? ' open' : ''}${hovered ? ' hovered' : ''}`}
      onClick={() => setVisible(!visible)}
      aria-label="Abrir panel de Aistrix"
      title="Aistrix - Asistente IA"
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => setHovered(false)}
    >
      <img
        src={aistrix}
        alt="Aistrix"
      />
      {hovered && <span className="aistrix-tabbutton__shortcut">Ctrl + A</span>}
    </button>
  );
}