import React from 'react';
import aistrix from '@/img/aistrix.jpg';

export default function WelcomeBox() {
  return (
    <div className="welcomebox">
      <div className="welcomebox__header">
        <img src={aistrix} alt="Aistrix" />
        <h2>¿Qué es Aistrix?</h2>
      </div>
      <div className="welcomebox__body">
        <p>
          <b>Aistrix</b> es tu asistente de programación para Moodle.<br />
          Actualmente puede analizar los errores de tu código VPL y explicártelos de forma sencilla.<br /><br />
          <b>¿Cómo usarlo?</b><br />
          Haz clic en una <b>carta de acción</b> (como "Analizar código") para recibir ayuda personalizada sobre el error que aparece en tu código.<br /><br />
          ¡Pronto habrá más funcionalidades!
        </p>
      </div>
    </div>
  );
}