<?php
/**
 * =====================================================================
 *  inc/funciones.php · Funciones del tablón · PLANTILLA DEL ALUMNO
 * =====================================================================
 *  PARTE 1 (UD3): decisión con match, recorridos con foreach, arrays.
 *  PARTE 2 (UD4): funciones propias con tipos, parámetros y retorno.
 *
 *  Cada función tiene un "return provisional" para que la web funcione
 *  desde el primer día. Sustitúyelo por tu código y borra el TODO.
 *  NO cambies los nombres ni las firmas: el resto de páginas las usan.
 */

declare(strict_types=1);

/** Rangos válidos, de menor a mayor dificultad. (Dado) */
const RANGOS = ['E', 'D', 'C', 'B', 'A', 'S'];

/** Escapa texto para pintarlo en HTML. (Dado: úsalo SIEMPRE al pintar datos) */
function e(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

/**
 * PARTE 1 · Nombre del rango.
 * E Novato · D Aprendiz · C Veterano · B Experto · A Élite · S Leyenda
 * TODO: usa match. Pregunta para la memoria: ¿pones default? ¿por qué?
 */
function etiquetaRango(string $rango): string
{
    return match ($rango) {
        'E' => 'Novato',
        'D' => 'Aprendiz',
        'C' => 'Veterano',
        'B' => 'Experto',
        'A' => 'Élite',
        'S' => 'Leyenda',
    };
    // Sin default: si llega un rango desconocido PHP lanza UnhandledMatchError,
    // lo que me avisa de que algo va mal en los datos, en lugar de ocultarlo.
}

/**
 * PARTE 1 · Clase CSS según dificultad.
 * E y D → 'facil' · C y B → 'media' · A y S → 'dificil'
 * TODO: usa match con varios valores por brazo.
 */
function claseRango(string $rango): string
{
    return match ($rango) {
        'E', 'D' => 'facil',
        'C', 'B' => 'media',
        'A', 'S' => 'dificil',
    };
}

/**
 * PARTE 1 · Formatea monedas de oro: 5000 → "5.000 mo".
 * TODO: busca number_format en el manual de PHP.
 */
function formatearOro(int $oro): string
{
    return number_format($oro, 0, ',', '.') . ' mo';
}
