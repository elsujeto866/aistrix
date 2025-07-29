# Local Aistrix Plugin for Moodle

Plugin local de Moodle que integra VPL (Virtual Programming Lab) con un sistema de IA vía webhook para análisis de código y feedback automático.

## Descripción

Aistrix es un asistente de programación integrado en Moodle que permite a los estudiantes enviar sus entregas de VPL a un servicio de IA externo para obtener:

- Análisis de errores de sintaxis y lógica
- Explicaciones claras de problemas en el código
- Sugerencias de mejoras y correcciones
- Feedback educativo personalizado

## Características Principales

### ✅ Funcionalidades Implementadas

- **Integración con VPL**: Obtención automática de datos de entregas de estudiantes
- **Extracción de archivos**: Lectura de código fuente desde file storage y moodledata
- **Resultados de ejecución**: Captura de salidas de compilación y ejecución
- **Webhook configurable**: Envío de datos JSON a servicios de IA externos
- **Frontend React**: Interfaz moderna y responsive para estudiantes
- **Feedback de IA**: Visualización de explicaciones en el frontend
- **Validación de notas**: Prevención de envíos innecesarios cuando ya se obtuvo la nota máxima
- **Configuración flexible**: Parámetros configurables desde la administración

### 🚧 Funcionalidades en Desarrollo

- **Control de uso**: Sistema de límites de consultas por usuario (implementación comentada)
- **Estadísticas de uso**: Dashboard para administradores
- **Múltiples proveedores de IA**: Soporte para diferentes servicios de IA

## Arquitectura

### Backend (PHP)

```
local/aistrix/
├── classes/
│   ├── external/          # Servicios web (API)
│   ├── services/          # Lógica de negocio
│   └── output/           # Renderables para templates
├── db/                   # Definiciones de base de datos
├── lang/                 # Cadenas de idioma
└── templates/           # Templates Mustache
```

### Frontend (React + SCSS)

```
assets/
├── js/
│   ├── main.jsx         # Punto de entrada
│   └── components/      # Componentes React
└── scss/               # Estilos SCSS
```

### Build System

- **Vite**: Bundling y desarrollo en caliente
- **Babel**: Transpilación de JSX
- **SCSS**: Preprocesador CSS

## Instalación

### Requisitos

- Moodle 4.0+
- Plugin VPL instalado y configurado
- Node.js 16+ (para desarrollo)
- Acceso a webhook de IA externo

### Pasos de Instalación

1. **Clonar el plugin**:
   ```bash
   cd /path/to/moodle/local/
   git clone [repository] aistrix
   ```

2. **Instalar dependencias de desarrollo** (opcional):
   ```bash
   cd aistrix
   npm install
   ```

3. **Construir assets** (producción):
   ```bash
   npm run build
   ```

4. **Instalar desde Moodle**:
   - Ir a Administración → Plugins → Instalar plugins
   - O ejecutar: `php admin/cli/upgrade.php`

5. **Configurar el plugin**:
   - Ir a Administración → Plugins → Plugins locales → Aistrix
   - Configurar URL del webhook de IA
   - Establecer límites de uso (opcional)

## Configuración

### Parámetros Principales

| Parámetro | Descripción | Valor por defecto |
|-----------|-------------|-------------------|
| `webhook_url` | URL del servicio de IA | (vacío) |
| `max_executions` | Máximo consultas por usuario | 10 |
| `reset_period` | Período de reseteo (horas) | 24 |

### Variables de Entorno (Desarrollo)

```bash
# Modo desarrollo para Vite
export VITE_DEV=1
```

## Uso

### Para Estudiantes

1. Acceder a `/local/aistrix/view.php`
2. Seleccionar un VPL con entregas realizadas
3. Hacer clic en "Analizar código"
4. Revisar el feedback de la IA

### Para Administradores

- Configurar webhooks en la administración del plugin
- Monitorear logs de Moodle para debugging
- Utilizar scripts auxiliares para mantenimiento

## Desarrollo

### Entorno de Desarrollo

```bash
# Instalar dependencias
npm install

# Modo desarrollo (watch)
npm run dev

# Build para producción  
npm run build
```

### Estructura de Archivos Clave

#### Core PHP Files

- `version.php` - Información del plugin y versión
- `settings.php` - Configuración administrativa
- `lib.php` - Funciones principales del plugin
- `view.php` - Página principal del plugin

#### Database

- `db/install.xml` - Definición de tablas
- `db/upgrade.php` - Scripts de actualización
- `db/access.php` - Permisos y capacidades
- `db/services.php` - Servicios web

#### Services

- `classes/services/vpl_service.php` - Integración con VPL
- `classes/services/webhook_service.php` - Comunicación con IA
- `classes/services/usage_service.php` - Control de uso (pendiente)

#### External API

- `classes/external/process_student_vpl.php` - Procesar entrega de estudiante
- `classes/external/get_student_vpls.php` - Obtener VPLs disponibles

#### Frontend

- `assets/js/main.jsx` - Aplicación React principal
- `assets/js/components/Panel.jsx` - Panel principal
- `assets/js/components/ActionCard.jsx` - Botones de acción
- `assets/js/components/ExplanationBox.jsx` - Feedback de IA

## Scripts Auxiliares

### Para Debugging y Mantenimiento

```bash
# Verificar servicios web registrados
php verify_services.php

# Forzar registro de servicios
php force_register_services.php

# Crear tabla de uso manualmente
php create_table_manual.php

# Resetear plugin completamente
php reset_plugin.php

# Probar procesamiento de entregas
php test_process_student.php

# Debug obtención de datos VPL
php debug_get_student_vpl.php
```

## API del Webhook

### Formato de Datos Enviados

```json
{
  "vpl": {
    "id": 123,
    "name": "Ejercicio 1 - Hello World",
    "course": {
      "id": 456,
      "name": "Programación I"
    }
  },
  "submission": {
    "id": 789,
    "student": {
      "id": 101,
      "firstname": "Juan",
      "lastname": "Pérez"
    },
    "datesubmitted": 1640995200,
    "grade": 8.5,
    "files": [
      {
        "filename": "main.c",
        "content": "#include <stdio.h>\nint main() {\n    printf(\"Hello World\");\n    return 0;\n}"
      }
    ],
    "execution_results": {
      "execution_output": "Hello World",
      "compilation_output": "gcc main.c -o main",
      "stdout": "Hello World",
      "stderr": null
    }
  }
}
```

### Respuesta Esperada del Webhook

```json
{
  "success": true,
  "feedback": "Tu código está bien estructurado. Sin embargo, te falta un salto de línea al final del printf para una mejor presentación.",
  "suggestions": ["Agregar \\n al final del printf", "Considerar usar return EXIT_SUCCESS"]
}
```

## Troubleshooting

### Problemas Comunes

1. **Servicios web no registrados**:
   ```bash
   php force_register_services.php
   ```

2. **Error en tabla de uso**:
   ```bash
   php create_table_manual.php
   ```

3. **Assets no cargan**:
   ```bash
   npm run build
   ```

4. **Webhook no responde**:
   - Verificar URL en configuración
   - Revisar logs de Moodle
   - Probar con `debug_get_student_vpl.php`

### Logs Importantes

```bash
# Logs de Moodle
tail -f /path/to/moodle/moodledata/error.log | grep AISTRIX

# Logs de desarrollo
tail -f /var/log/apache2/error.log
```

## Contribución

### Workflow de Desarrollo

1. Fork del repositorio
2. Crear rama feature: `git checkout -b feature/nueva-funcionalidad`
3. Desarrollar y probar
4. Commit: `git commit -m "feat: descripción de la funcionalidad"`
5. Push: `git push origin feature/nueva-funcionalidad`
6. Crear Pull Request

### Estándares de Código

- **PHP**: Seguir estándares de Moodle
- **JavaScript**: ESLint + Prettier
- **CSS/SCSS**: BEM methodology
- **Commits**: Conventional Commits

## Licencia

GNU GPL v3 or later

## Créditos

- Desarrollado para integración VPL-IA en Moodle
- Utiliza React para frontend moderno
- Integración con sistema de webhooks para IA

## Roadmap

### v1.1 (Próxima versión)
- [ ] Reactivar sistema de control de uso
- [ ] Dashboard de estadísticas para administradores
- [ ] Soporte para múltiples idiomas

### v1.2 (Futuro)
- [ ] Múltiples proveedores de IA
- [ ] Cache de respuestas de IA
- [ ] Integración con otros tipos de actividades

### v2.0 (Futuro lejano)
- [ ] IA en tiempo real durante la programación
- [ ] Sugerencias proactivas de código
- [ ] Análisis de estilo y buenas prácticas
