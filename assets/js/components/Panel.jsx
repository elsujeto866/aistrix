import React, { useState } from 'react';
import ActionCard from './ActionCard';
import ExplanationBox from './ExplanationBox';
import WelcomeBox from './WelcomeBox';

export default function Panel({ 
    visible, 
    onClose, 
    loading, 
    result, 
    error, 
    onSendVPL, 
    onSendStudentVPL,
    username, 
    fullname,
    studentVpls,
    loadingVpls,
    selectedVpl,
    onSelectVpl
}) {

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
        } else if (type === "enviar_vpl_estudiante") {
            if (selectedVpl) {
                onSendStudentVPL(selectedVpl.id);
            }
        } else if (type === "enviar_vpl_admin") {
            onSendVPL();
        }
    }

    function handleBackToWelcome() {
        setShowExplanation(false);
    }

    // Función para formatear la fecha
    function formatDate(timestamp) {
        return new Date(timestamp * 1000).toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
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
                
                {/* Selector de VPL del estudiante */}
                {loadingVpls ? (
                    <div className="vpl-selector loading">
                        <p>Cargando tus actividades VPL...</p>
                    </div>
                ) : studentVpls.length > 0 ? (
                    <div className="vpl-selector">
                        <h3>📚 Tus actividades VPL</h3>
                        <select 
                            value={selectedVpl?.id || ''} 
                            onChange={(e) => {
                                const vpl = studentVpls.find(v => v.id == e.target.value);
                                onSelectVpl(vpl);
                            }}
                            className="vpl-select"
                        >
                            {studentVpls.map(vpl => (
                                <option key={vpl.id} value={vpl.id}>
                                    {vpl.name} ({vpl.coursename}) - {vpl.submission_count} entrega(s)
                                </option>
                            ))}
                        </select>
                        
                        {selectedVpl && (
                            <div className="vpl-info">
                                <p><strong>Última entrega:</strong> {formatDate(selectedVpl.last_submission)}</p>
                                <p><strong>Total entregas:</strong> {selectedVpl.submission_count}</p>
                                <p><strong>Curso:</strong> {selectedVpl.coursename}</p>
                            </div>
                        )}
                    </div>
                ) : (
                    <div className="vpl-selector no-vpls">
                        <h3>📚 Actividades VPL</h3>
                        <p>No tienes entregas en actividades VPL aún.</p>
                        <p>Cuando realices una entrega, podrás enviar tu código para análisis.</p>
                    </div>
                )}

                {/* ActionCards */}
                <ActionCard
                    icon={<span role="img" aria-label="libro">📖</span>}
                    title="Explicar error"
                    description="Recibe una explicación detallada del error que aparece en tu código."
                    onClick={() => handleActionCardClick("explicar")}
                />

                {/* ActionCard para enviar VPL del estudiante */}
                <ActionCard
                    icon={<span role="img" aria-label="código">💻</span>}
                    title="Analizar mi código VPL"
                    description={selectedVpl 
                        ? `Enviar código de "${selectedVpl.name}" para análisis con IA`
                        : "Selecciona una actividad VPL para análisis"
                    }
                    onClick={() => handleActionCardClick("enviar_vpl_estudiante")}
                    disabled={!selectedVpl || loading}
                />

                {/* ActionCard para enviar todos los VPL (admin/profesor) */}
                <ActionCard
                    icon={<span role="img" aria-label="datos">📊</span>}
                    title="Procesar todos los VPL"
                    description="Enviar datos de todos los VPL para procesamiento masivo (requiere permisos)"
                    onClick={() => handleActionCardClick("enviar_vpl_admin")}
                    disabled={loading}
                />

                {/* Mostrar loading, resultado o error */}
                {loading && (
                    <div className="status-message loading">
                        <p>⏳ Procesando...</p>
                    </div>
                )}

                {result && (
                    <div className="status-message success">
                        <p>✅ {result}</p>
                    </div>
                )}

                {error && (
                    <div className="status-message error">
                        <p>❌ {error}</p>
                    </div>
                )}

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
