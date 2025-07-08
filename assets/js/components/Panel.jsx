import React, { useState } from 'react';
import ActionCard from './ActionCard';
import ExplanationBox from './ExplanationBox';
import WelcomeBox from './WelcomeBox';

export default function Panel({ visible, onClose, loading, result, error, onSendVPL, username, fullname }) {

    const [showExplanation, setShowExplanation] = useState(false);
    // Estado para el contenido del ExplanationBox
    const [explanation, setExplanation] = useState({
        errorTitle: "Aistrix",
        errorMessage: "",
        description: "",
        footer: ""
    });

    // Handler para cuando se presiona una ActionCard
    function handleActionCardClick(type) {
        if (type === "explicar") {
            setExplanation({
                errorTitle: "Explicar error",
                errorMessage: `main.c:4:1: error: expected ';' after expression`,
                description: `El mensaje de error indica que hace falta un punto y coma (';') después de la expresión en la línea 4 del archivo 'main.c'.`,
                footer: "Aistrix te acompaña en tu aprendizaje.<br />¡Vamos paso a paso!"
            });
            setShowExplanation(true);
        }
        // Puedes agregar más tipos para otras ActionCards
    }

    function handleBackToWelcome() {
        setShowExplanation(false);
    }

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
                    onClick={() => handleActionCardClick("explicar")}
                />
                {/* ...otras ActionCards... */}

                {!showExplanation && <WelcomeBox />}
                {showExplanation && (
                    <ExplanationBox
                        errorTitle={explanation.errorTitle}
                        errorMessage={explanation.errorMessage}
                        description={explanation.description}
                        onAistrixClick={handleBackToWelcome}
                    >
                        <span dangerouslySetInnerHTML={{ __html: explanation.footer }} />
                    </ExplanationBox>
                )}
            </div>
        </div>
    );
}
