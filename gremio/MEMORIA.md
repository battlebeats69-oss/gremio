# Memoria · Gremio de Aventureros

**Código de alumno/a:** 0613DAW-JAODLO

---

## 1. Funciones implementadas en la Parte 1

### `etiquetaRango($rango)`

Devuelve el nombre en texto del rango recibido. Lo hice con `match` porque es más limpio que un `switch` cuando solo hay que hacer una comparación por valor. No puse `default`: si llega un rango que no existe (como `'Z'`), PHP lanza un `UnhandledMatchError`, lo que me avisa de que algo va mal en los datos en lugar de ocultarlo silenciosamente.

### `claseRango($rango)`

Devuelve la clase CSS que corresponde al nivel de dificultad. También usé `match`, pero con varios valores en el mismo brazo separados por coma: `'E', 'D' => 'facil'`. Así se evita repetir código.

### `formatearOro($oro)`

Convierte un número entero a texto con el formato `5.000 mo`. Usé `number_format($oro, 0, ',', '.')` indicando que no quiero decimales, que el separador decimal es la coma (no se usa) y que el separador de miles es el punto.

---

## 2. Cómo se genera el tablón

En `tablon.php` recorro el array `$visibles` con un `foreach`. Para cada misión:

- Calculo la clase CSS con `claseRango()` y la uso en el `article`.
- Muestro el rango con `etiquetaRango()`.
- Controlar el singular y el plural: si `$mision['dias'] === 1` escribo `1 día`, si no `X días`.
- Escapo toda cadena que venga de los datos con `e()` antes de pintarla.
- Si la misión ya está en el zurrón muestro `✔ En tu zurrón`; si no, un formulario POST con el `id` oculto y el botón `Aceptar misión`.
- Si `$visibles` está vacío muestro el mensaje `Ninguna misión cumple el filtro.`

La tabla de misiones se genera teniendo en cuenta los filtros GET, de modo que el tablón puede mostrar las 10 misiones originales o un subconjunto filtrado.

---

## 3. Validación de los filtros GET

La URL puede contener lo que el usuario quiera escribir, así que nunca la uso directamente.

**`leerRangoFiltro($_GET)`**: compruebo que `$_GET['rango']` existe, lo convierto a mayúsculas y verifico que esté en el array `RANGOS`. Si no pasa la validación devuelvo `null`, y `filtrarMisiones` entiende que no hay filtro de rango.

**`leerOroMinimo($_GET)`**: uso `filter_var` con `FILTER_VALIDATE_INT` y las opciones `min_range => 0` y `max_range => 10000`. Si el valor no viene, no es un entero o está fuera del rango, devuelvo `0`.

Prueba: `tablon.php?rango=Z&oro=hola` → aparecen las 10 misiones sin ningún error PHP.

---

## 4. Funciones de filtrado y cálculo

**`filtrarMisiones()`**: recorro el array con `foreach` y voy añadiendo al resultado solo las misiones que pasan los dos filtros: si el rango no es `null` debe coincidir, y la recompensa debe ser mayor o igual al mínimo.

**`buscarMision()`**: recorro con `foreach` buscando la misión cuyo `id` coincida. Si la encuentro la devuelvo; si termino el bucle sin encontrarla devuelvo `null`.

**`totalRecompensa()`**: recorro el array de ids, llamo a `buscarMision()` por cada uno y sumo su recompensa. Si un id no existe (devuelve `null`) simplemente no sumo nada.

---

## 5. Aceptar y abandonar misiones

**Aceptar (en `tablon.php`)**: el formulario envía el id por POST. Leo `$_POST['id']`, busco la misión con `buscarMision()` y compruebo dos casos de error: que no exista, o que ya esté en el zurrón. Si todo va bien la añado al array `$_SESSION['zurron']`. En todos los casos muestro un mensaje flash y redirijo con `header('Location: tablon.php')` + `exit`.

**Abandonar (en `zurron.php`)**: busco la posición del id en el zurrón con `array_search()`, hago `unset` de esa posición y reindexo con `array_values()` para que no queden huecos en el array. Luego flash y redirección a `zurron.php`.

---

## 6. Patrón Post/Redirect/Get

Después de cada POST (aceptar o abandonar) siempre redirijo con `header('Location: ...)` y llamo a `exit` inmediatamente. Esto hace que el navegador cambie la petición POST por una GET, así si el usuario pulsa F5 no se reenvía el formulario ni se duplica la acción. El mensaje de confirmación lo guardo con `mensajeFlash()` en sesión y lo leo en la siguiente carga con `leerFlash()`, que además lo borra para que no aparezca dos veces.

---

## 7. Sesión frente a cookie

En `$_SESSION` guardo los datos de la sesión del aventurero: quién es, su zurrón y la hora de entrada. Estos datos son privados y viven en el servidor; el navegador solo recibe una cookie con el id de sesión (`PHPSESSID`).

En las cookies propias guardo datos no sensibles del navegador: el tema elegido (`gremio_tema`) y el nombre de usuario para rellenar el formulario de login (`gremio_usuario`). No guardo el zurrón en una cookie porque podría manipularse desde el navegador.

---

## 8. Seguridad del login

- **`autenticar()`** usa una consulta preparada con `:usuario`. Si alguien escribe `' OR '1'='1` como usuario, PDO lo trata como texto literal, no como SQL, así que no encuentra ningún usuario y devuelve `null`.
- Llamo a `session_regenerate_id(true)` justo después de autenticar para cambiar el id de sesión. Así evito ataques de fijación de sesión: si alguien capturó el id anterior, ya no sirve.
- El mensaje de error es el mismo tanto si falla el usuario como si falla la contraseña (`'Usuario o contraseña incorrectos.'`). Si dijera cuál falla, le estaría dando información al atacante sobre qué usuarios existen.

---

## 9. Mi aventurero

- Usuario: `jdelaosa`
- Clase: `Explorador`
- Hash generado con: `docker compose exec www php -r "echo password_hash('MiClave2026', PASSWORD_DEFAULT), PHP_EOL;"`

---

## 10. Uso de IA

Se ha utilizado inteligencia artificial como herramienta de apoyo en determinados procesos del proyecto, incluyendo la revisión del funcionamiento, la detección de posibles mejoras y la generación y estructuración de la documentación. Su uso ha servido como complemento al trabajo realizado, manteniendo la supervisión y validación humana de los resultados.

---


## 11. Pruebas realizadas

| Prueba | Resultado esperado | Resultado obtenido |
|---|---|---|
| Abrir `tablon.php` | 10 tarjetas con bordes de colores | ✅ 10 misiones, bordes verde/naranja/rojo |
| Filtrar rango B y oro mínimo 500 | 2 misiones | ✅ Aparecen M05 y M10 |
| `tablon.php?rango=Z&oro=hola` | 10 misiones, sin errores PHP | ✅ Funciona, filtros ignorados |
| Aceptar «Bandidos del desfiladero» y «El dragón de Obsidiana» | Total 5.650 mo | ✅ Muestra 5.650 mo |
| Aceptar la misma misión dos veces | No se duplica | ✅ Aviso «Ya tienes esa misión en el zurrón» |
| Pulsar F5 tras aceptar | No reenvía el formulario | ✅ El navegador hace GET |
| Abandonar una misión | Desaparece del zurrón y el total se actualiza | ✅ Funciona correctamente |
| Entrar en `tablon.php` sin login | Redirige a `login.php?motivo=privado` | ✅ Muestra el aviso |
| Login con contraseña mala | Mensaje de error genérico | ✅ No revela si el usuario existe |
| Login correcto | Redirige al tablón con mensaje de bienvenida | ✅ Flash visible una sola vez |
| Cambiar tema a oscuro | El cuerpo cambia de clase | ✅ `body.tema-oscuro` aplicado |
| Cerrar sesión y volver al zurrón | Redirige al login | ✅ Sesión destruida |
| Recordar usuario y cerrar sesión | El campo usuario ya está relleno al volver | ✅ Cookie funciona |
| Contador de visitas | Sube 1 en cada recarga del tablón | ✅ Se muestra en la barra |
