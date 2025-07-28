import React, { useState, useEffect } from 'react';
import TabButton from './TabButton';
import Panel from './Panel';

export default function App() {
  // Obtener datos del usuario desde el DOM
  const container = document.getElementById('aistrix-root');
  const username = container?.dataset?.username || 'Usuario';
  const fullname = container?.dataset?.fullname || 'Usuario';

  // Estado para controlar la visibilidad del panel
  const [visible, setVisible] = useState(false);
  const [tabOpen, setTabOpen] = useState(false);
  const [loading, setLoading] = useState(false);
  const [result, setResult] = useState(null);
  const [error, setError] = useState(null);

  // Nuevos estados para VPL del estudiante
  const [studentVpls, setStudentVpls] = useState([]);
  const [loadingVpls, setLoadingVpls] = useState(false);
  const [selectedVpl, setSelectedVpl] = useState(null);

  // Sincroniza el estado del tab con el panel
  useEffect(() => {
    if (visible) {
      setTabOpen(true);
      // Cargar VPLs del estudiante cuando se abre el panel
      cargarVPLsEstudiante();
    } else {
      // Espera la duración de la transición del panel (ej: 300ms)
      const timeout = setTimeout(() => setTabOpen(false), 100);
      return () => clearTimeout(timeout);
    }
  }, [visible]);

  // Atajo de teclado Ctrl+A o Cmd+A para abrir/cerrar el panel
  useEffect(() => {
    function handleKeyDown(e) {
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'a') {
        e.preventDefault();
        setVisible((v) => !v);
      }
    }
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, []);

  // Función para cargar VPLs donde el estudiante tiene entregas
  async function cargarVPLsEstudiante() {
    setLoadingVpls(true);
    console.log('DEBUG: Iniciando cargarVPLsEstudiante');

    try {
      const params = {
        methodname: 'local_aistrix_get_student_vpls',
        args: {},
      };

      console.log('DEBUG: Enviando request con params:', params);

      const response = await fetch(
        M.cfg.wwwroot + '/lib/ajax/service.php?sesskey=' + M.cfg.sesskey,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          credentials: 'same-origin',
          body: JSON.stringify([params]),
        }
      );

      console.log('DEBUG: Response status:', response.status);
      console.log('DEBUG: Response headers:', response.headers);

      const data = await response.json();
      console.log('DEBUG: Data recibida completa:', data);
      console.log('DEBUG: Tipo de data:', typeof data);
      console.log('DEBUG: Es array:', Array.isArray(data));
      console.log('DEBUG: Longitud:', data?.length);
      console.log('DEBUG: data[0]:', data[0]);

      // Verificar si la respuesta tiene el formato correcto
      if (!data || !Array.isArray(data) || data.length === 0) {
        console.error('DEBUG: Formato de respuesta inválido');
        setError('Error: Respuesta del servidor en formato inválido');
        setStudentVpls([]);
        return;
      }

      if (data[0] && data[0].error) {
        console.error('DEBUG: Error en la respuesta:', data[0].exception);
        setError('Error al cargar VPLs: ' + data[0].exception.message);
        setStudentVpls([]);
      } else if (data[0] && data[0].data) {
        console.log('DEBUG: data[0].data:', data[0].data);
        console.log('DEBUG: data[0].data.vpls:', data[0].data.vpls);

        setStudentVpls(data[0].data.vpls || []);
        // Si hay VPLs, seleccionar el más reciente por defecto
        if (data[0].data.vpls && data[0].data.vpls.length > 0) {
          console.log(
            'DEBUG: Seleccionando VPL por defecto:',
            data[0].data.vpls[0]
          );
          setSelectedVpl(data[0].data.vpls[0]);
        } else {
          console.log('DEBUG: No hay VPLs para seleccionar');
        }
      } else {
        console.error('DEBUG: Estructura de respuesta inesperada:', data[0]);
        setError('Error: Estructura de respuesta inesperada del servidor');
        setStudentVpls([]);
      }
    } catch (e) {
      console.error('DEBUG: Error de red:', e);
      setError('Error de red al cargar VPLs');
      setStudentVpls([]);
    } finally {
      setLoadingVpls(false);
      console.log('DEBUG: cargarVPLsEstudiante terminado');
    }
  }

  // Función original para enviar todos los VPL (admin/profesor)
  async function enviarVPL(courseid = null) {
    setLoading(true);
    setResult(null);
    setError(null);
    try {
      const params = {
        methodname: 'local_aistrix_process_vpl',
        args: { courseid },
      };
      const response = await fetch(
        M.cfg.wwwroot + '/lib/ajax/service.php?sesskey=' + M.cfg.sesskey,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          credentials: 'same-origin',
          body: JSON.stringify([params]),
        }
      );
      const data = await response.json();
      if (data[0].error) {
        setError(data[0].exception.message);
      } else {
        setResult(data[0].data.message);
      }
    } catch (e) {
      setError('Error de red o inesperado');
    } finally {
      setLoading(false);
    }
  }

  // Nueva función para enviar VPL del estudiante actual
  async function enviarVPLEstudiante(vplid) {
    if (!vplid) {
      setError('Selecciona un VPL primero');
      return;
    }

    setLoading(true);
    setResult(null);
    setError(null);
    try {
      const params = {
        methodname: 'local_aistrix_process_student_vpl',
        args: { vplid: vplid },
      };
      const response = await fetch(
        M.cfg.wwwroot + '/lib/ajax/service.php?sesskey=' + M.cfg.sesskey,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          credentials: 'same-origin',
          body: JSON.stringify([params]),
        }
      );
      const data = await response.json();
      if (data[0].error) {
        setError(data[0].exception.message);
      } else {
        // Si hay feedback de la IA, mostrarlo; sino, mostrar el mensaje normal
        if (data[0].data.feedback) {
          setResult({
            type: 'feedback',
            message: data[0].data.message,
            feedback: data[0].data.feedback,
            vplname: data[0].data.vplname,
            studentname: data[0].data.studentname
          });
        } else {
          setResult(data[0].data.message);
        }
      }
    } catch (e) {
      setError('Error de red o inesperado');
    } finally {
      setLoading(false);
    }
  }

  function handleClose() {
    setVisible(false);
    setResult(null);
    setError(null);
  }

  return (
    <div className="aistrix-app">
      <TabButton visible={tabOpen} setVisible={setVisible} />
      <Panel
        visible={visible}
        onClose={handleClose}
        loading={loading}
        result={result}
        error={error}
        onSendVPL={() => enviarVPL()}
        onSendStudentVPL={(vplid) => enviarVPLEstudiante(vplid)}
        username={username}
        fullname={fullname}
        studentVpls={studentVpls}
        loadingVpls={loadingVpls}
        selectedVpl={selectedVpl}
        onSelectVpl={setSelectedVpl}
      />
    </div>
  );
}
