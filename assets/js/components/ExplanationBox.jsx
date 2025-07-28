import React from 'react';
import aistrix from '@/img/aistrix.png';

export default function ExplanationBox({ errorTitle, description, children, onAistrixClick }) {
  return (
    <div className='explanationbox__aistrix'>
      <div className='explanationbox__aistrix-header'>
        <div className="explanationbox__aistrix-img-wrapper" onClick={onAistrixClick} title="Volver a la bienvenida">
          <img src={aistrix} alt="Aistrix" />
        </div>
        <div className='explanationbox__title'>{errorTitle}</div>
      </div>

      <div className="explanationbox">

        <div className="explanationbox__section">
          <div className="explanationbox__section-title">Ayuda</div>
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