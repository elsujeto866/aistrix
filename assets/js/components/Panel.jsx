import React from 'react';
import ActionCard from './ActionCard';

export default function Panel({ visible, onClose, loading, result, error, onSendVPL, username, fullname }) {
  return (
    <div className={`aistrix-panel ${visible ? 'open' : ''}`}>
      {/* Header del panel */}
      <div className="aistrix-panel__header">
        <h2>Bienvenido, {username} 👋</h2>
        <p>Soy Aistrix tu asistente de programación.</p>
        <p className="user-info">Usuario: {fullname}</p>
        <button 
          className="aistrix-panel__close" 
          onClick={onClose}
          aria-label="Cerrar panel"
        >
          ×
        </button>
      </div>
      
      {/* Contenido del panel */}
      <div className="aistrix-panel__content">
        
        <ActionCard
          icon={<span role="img" aria-label="libro">📖</span>}
          title="Explicar error"
          description="Recibe una explicación detallada del error que aparece en tu código."
          onClick={onSendVPL}
        />
        
        {result && (
          <div className="aistrix-panel__message aistrix-panel__message--success">
            {result}
          </div>
        )}
        
        {error && (
          <div className="aistrix-panel__message aistrix-panel__message--error">
            {error}
          </div>
        )}

        
      </div>
    </div>
  );
}
