<?php
// Establecer la conexión con la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_planes"; // Nombre de la base de datos que creaste

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Variable para el mensaje
$mensaje = "";

// Comprobar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['plan'])) {
        // Recibir los datos del formulario
        $plan = $_POST['plan'];  // Nombre del plan
        $fecha = $_POST['fecha']; // Fecha del plan

        // Escapar caracteres especiales para evitar inyecciones SQL
        $plan = $conn->real_escape_string($plan);
        $fecha = $conn->real_escape_string($fecha);

        // Insertar el plan en la base de datos
        $sql = "INSERT INTO planes (nombre, fecha) VALUES ('$plan', '$fecha')";

        if ($conn->query($sql) === TRUE) {
            $mensaje = "Nuevo plan guardado correctamente"; // Mensaje de éxito
        } else {
            $mensaje = "Error: " . $sql . "<br>" . $conn->error; // En caso de error
        }
    }

    // Marcar como hecho
    if (isset($_POST['hecho'])) {
        $id_plan = $_POST['hecho'];
        $sql = "UPDATE planes SET hecho = 1 WHERE id = $id_plan";
        $conn->query($sql);
    }

    // Eliminar plan
    if (isset($_POST['eliminar'])) {
        $id_plan = $_POST['eliminar'];
        $sql = "DELETE FROM planes WHERE id = $id_plan";
        $conn->query($sql);
    }
}

// Obtener todos los planes desde la base de datos
$sql = "SELECT * FROM planes";
$result = $conn->query($sql);

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilo.css">
    <link rel="icon" href="Imagenes/favicon.png" type="image/x-icon">
    <title>Nuestros recuerdos</title>
    <style>
        .plan-actions {
            display: inline-block;
            margin-left: 10px;
        }
        .mensaje {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <a href="principal.html"><h1>Zona Segura</h1></a>
        <div class="menu">
            <a href="imagenes.html">Imágenes</a>
            <a href="videos.html">Videos</a>
            <a href="planes.html">Planes</a>
            <a href="mapa.html">Mapa</a>
        </div>
    </header>

    <main class="container">
        <section class="plan-section">
            <h2 style="color:white;">Gestión de Planes</h2>
            <form id="planForm" class="contact-form" action="guardar_plan.php" method="POST">
                <input type="text" name="plan" placeholder="Nombre del plan..." required>
                <input type="date" name="fecha" required>
                <button type="submit">Agregar Plan</button>
            </form>

            <!-- Mostrar el mensaje debajo del formulario -->
            <?php if (!empty($mensaje)): ?>
                <div class="mensaje">
                    <p><?php echo $mensaje; ?></p>
                </div>
            <?php endif; ?>

            <!-- Mostrar los planes -->
            <ul id="planList" class="plan-list">
                <?php
                if ($result->num_rows > 0) {
                    // Mostrar los planes guardados
                    while($row = $result->fetch_assoc()) {
                        $plan_class = $row['hecho'] == 1 ? 'hecho' : '';
                        echo "<li class='$plan_class'>";
                        echo $row['nombre'] . " - Fecha: " . $row['fecha'];

                        // Botones de acción: Marcar como Hecho y Eliminar
                        echo "<div class='plan-actions'>";

                        // Botón de "Marcar como hecho"
                        if ($row['hecho'] == 0) {
                            echo "<form action='guardar_plan.php' method='POST' style='display:inline;'>
                                    <button type='submit' name='hecho' value='" . $row['id'] . "'>Marcar como Hecho</button>
                                  </form>";
                        }

                        // Botón de "Eliminar"
                        echo "<form action='guardar_plan.php' method='POST' style='display:inline;'>
                                <button type='submit' name='eliminar' value='" . $row['id'] . "'>Eliminar</button>
                              </form>";

                        echo "</div>"; // Cerrar el div de acciones
                        echo "</li>";
                    }
                } else {
                    echo "<li>No hay planes disponibles.</li>";
                }
                ?>
            </ul>
        </section>
    </main>

    <footer>
        <p>&copy; Todas estas imágenes están protegidas por la seguridad de los novios</p>
    </footer>
</body>
</html>
