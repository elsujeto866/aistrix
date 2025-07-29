<?php
/**
 * Plugin upgrade script para local_aistrix
 *
 * Este archivo contiene la función que maneja las actualizaciones del plugin
 * cuando se incrementa la versión en version.php.
 *
 * Moodle ejecuta automáticamente esta función cuando detecta que la versión
 * del plugin ha cambiado y necesita actualizar la base de datos u otros recursos.
 *
 * @package    local_aistrix
 * @copyright  2024 EPN
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Función principal de upgrade del plugin
 *
 * Esta función se ejecuta automáticamente cuando Moodle detecta que
 * $plugin->version en version.php ha sido incrementado.
 *
 * @param int $oldversion La versión anterior del plugin instalada en el sistema
 * @return bool True si el upgrade fue exitoso, false en caso contrario
 */
function xmldb_local_aistrix_upgrade($oldversion) {
    global $DB;
    
    // Obtener el database manager para operaciones de BD
    $dbman = $DB->get_manager();
    
    // Ejemplo de upgrade condicional basado en la versión anterior
    /*
    if ($oldversion < 2025072800) {
        // Código para upgrade desde versiones anteriores a 2025072800
        // Ejemplo: crear nueva tabla, agregar campos, etc.
        
        // Define la tabla
        $table = new xmldb_table('local_aistrix_usage');
        
        // Si la tabla no existe, crearla
        if (!$dbman->table_exists($table)) {
            // Crear tabla usando install.xml o definición manual
            // $dbman->create_table($table);
        }
        
        // Guardar punto de actualización
        upgrade_plugin_savepoint(true, 2025072800, 'local', 'aistrix');
    }
    */
    
    // NOTA: Para futuras actualizaciones, agregar bloques if similares
    // con versiones incrementales. Ejemplo:
    //
    // if ($oldversion < 2025080100) {
    //     // Cambios para versión 2025080100
    //     upgrade_plugin_savepoint(true, 2025080100, 'local', 'aistrix');
    // }
    //
    // if ($oldversion < 2025080200) {
    //     // Cambios para versión 2025080200
    //     upgrade_plugin_savepoint(true, 2025080200, 'local', 'aistrix');
    // }
    
    return true; // Upgrade completado exitosamente
}

/**
 * GUÍA PARA UPGRADES FUTUROS:
 *
 * 1. Incrementa $plugin->version en version.php
 * 2. Agrega un bloque if con la nueva versión aquí
 * 3. Incluye todo el código necesario para el upgrade
 * 4. Llama a upgrade_plugin_savepoint() al final del bloque
 * 5. Los usuarios existentes ejecutarán automáticamente el upgrade
 *
 * TIPOS DE OPERACIONES COMUNES EN UPGRADES:
 * - Crear/modificar tablas de base de datos
 * - Agregar/eliminar campos de tablas existentes
 * - Migrar datos entre versiones
 * - Limpiar cachés o configuraciones obsoletas
 * - Registrar nuevos servicios web
 */