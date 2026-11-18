<?php
/**
 * =====================================================================
 *  zurron.php · Mi zurrón: misiones aceptadas · PLANTILLA DEL ALUMNO
 * =====================================================================
 *  PARTE 2 (listar y total) · PARTE 3 (los datos viven en $_SESSION)
 */

declare(strict_types=1);

require_once __DIR__ . '/inc/funciones.php';
require_once __DIR__ . '/inc/sesion.php';

iniciarSesion();
$aventurero = requiereLogin();
$misiones   = require __DIR__ . '/inc/datos_misiones.php';
$_SESSION['zurron'] ??= [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id  = $_POST['id'] ?? '';
    $pos = array_search($id, $_SESSION['zurron'], true);
    if ($pos !== false) {
        unset($_SESSION['zurron'][$pos]);
        $_SESSION['zurron'] = array_values($_SESSION['zurron']);
        mensajeFlash('Has abandonado la misión.');
    }
    header('Location: zurron.php');
    exit;
}

$ids   = $_SESSION['zurron'];
$total = totalRecompensa($misiones, $ids);
$flash = leerFlash();

$titulo = 'Mi zurrón';
require __DIR__ . '/inc/cabecera.php';
?>
<h1>Mi zurrón</h1>
<?php if ($flash !== null): ?><p class="aviso"><?= e($flash) ?></p><?php endif; ?>

<?php
if ($ids === []):
?>
    <p>Tu zurrón está vacío. <a href="tablon.php">Ve al tablón</a> para aceptar misiones.</p>
<?php
else:
?>
    <table>
        <thead>
            <tr>
                <th>Misión</th>
                <th>Rango</th>
                <th>Recompensa</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($ids as $id):
            $mision = buscarMision($misiones, $id);
            if ($mision === null) continue;
        ?>
            <tr>
                <td><?= e($mision['titulo']) ?></td>
                <td><?= e($mision['rango']) ?> · <?= e(etiquetaRango($mision['rango'])) ?></td>
                <td class="oro"><?= e(formatearOro($mision['recompensa'])) ?></td>
                <td>
                    <form method="post" action="zurron.php">
                        <input type="hidden" name="id" value="<?= e($mision['id']) ?>">
                        <button type="submit" class="secundario">Abandonar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2"><strong>Total</strong></td>
                <td class="oro"><strong><?= e(formatearOro($total)) ?></strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
<?php endif; ?>
<p class="nota">El zurrón vive en la sesión: si sales del gremio, se vacía.</p>
<?php require __DIR__ . '/inc/pie.php'; ?>
