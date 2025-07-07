import React, { useState, useEffect } from 'react';

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
        <button 
          className="aistrix-panel__action-btn"
          onClick={onSendVPL} 
          disabled={loading}
        >
          {loading ? 'Enviando...' : 'Enviar datos VPL'}
        </button>
        
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
