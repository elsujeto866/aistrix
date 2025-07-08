import React from 'react';
import aistrix from '@/img/aistrix.png';

export default function ExplanationBox({ errorTitle, errorMessage, description, children, onAistrixClick }) {
  return (
    <div className='explanationbox__aistrix'>
      <div className='explanationbox__aistrix-header'>
        <div className="explanationbox__aistrix-img-wrapper" onClick={onAistrixClick} title="Volver a la bienvenida">
          <img src={aistrix} alt="Aistrix" />
        </div>
        <h3>{errorTitle}</h3>
      </div>

      <div className="explanationbox">
        <div className="explanationbox__section">
          <div className="explanationbox__section-title">Error encontrado</div>
          <div className="explanationbox__error-message">
            <span className="explanationbox__error-icon" role="img" aria-label="error">❌</span>
            <span>{errorMessage}</span>
          </div>
        </div>

        <div className="explanationbox__section">
          <div className="explanationbox__section-title">Descripción</div>
          <div className="explanationbox__description">
            {description}
          </div>
        </div>

        {children && (
          <div className="explanationbox__footer">
            {children}
          </div>
        )}
      </div>
    </div>
  );
}