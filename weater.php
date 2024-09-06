<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clima</title>
    <link rel="stylesheet" href="../pruebaclima/css/apiestilos.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
session_start();
require 'config.php';
$username = $_SESSION['user_id'];
// Tu clave API de OpenWeatherMap
$apiKey = 'fb238023cdd7bb55302d7326c312cd93'; // Reemplaza con tu clave API
$city = 'Cali'; // La ciudad para la cual quieres obtener el clima
$unit = 'imperial'; // Unidad de temperatura: 'metric' para Celsius, 'imperial' para Fahrenheit

// URL de la API
$url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units={$unit}";

// Inicializa cURL
$ch = curl_init();

// Configura cURL para hacer la solicitud
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

// Ejecuta la solicitud
$response = curl_exec($ch);

// Verifica si hubo errores en la solicitud
if(curl_errno($ch)) {
    echo 'Error en la solicitud: ' . curl_error($ch);
    exit();
}

// Cierra cURL
curl_close($ch);

function fahrenheitToCentrigrados($fahrenheit) {
    return ($fahrenheit - 32) / 1.8;
}

// Decodifica la respuesta JSON
$data = json_decode($response, true);

// Verifica si la solicitud fue exitosa
if ($data['cod'] === 200) {
    // Extrae datos relevantes
    $temperature = $data['main']['temp'];
    echo $temperature;
    $centigrados = fahrenheitToCentrigrados($temperature);
    $description = $data['weather'][0]['description'];
    $city = $data['name'];
    
    echo "<h1>Clima en {$city}</h1>";
    echo "<p>Temperatura: {$centigrados} °C</p>";
    echo "<p>Descripción del Clima: {$description}</p>";
    $fecActual = date("Y-m-d H:i:s");
    $stmt = $pdo->prepare("INSERT INTO clima_user (ciudad, temp_centigrados, desc_clima, fec_consulta, user_reg) VALUES (:ciudad, :temp_centigrados, :desc_clima, :fec_consulta, :user_reg)");
    $stmt->execute(['ciudad' => $city, 'temp_centigrados' => $centigrados, 'desc_clima'=> $description, 'fec_consulta' => $fecActual, 'user_reg' => $username ]);
} else {
    // Muestra mensaje de error si la solicitud falló
    echo "<p>Error: " . $data['message'] . "</p>";
}
?>
<h1>Obtener Ubicación del Usuario</h1>
    <button id="getLocation" class="btnUbicacion">Obtener Ubicación</button>
    <p id="location"></p>

    <script>
        document.getElementById('getLocation').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                document.getElementById('location').innerHTML = "La geolocalización no es soportada por este navegador.";
            }
        });

        function showPosition(position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            document.getElementById('location').innerHTML = `Latitud: ${lat}<br>Longitud: ${lon}`;

            // Envía la ubicación al servidor usando fetch
            fetch('save_location.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `latitude=${lat}&longitude=${lon}`
            })
            .then(response => response.text())
            .then(data => console.log(data))
            .catch(error => console.error('Error:', error));
        }

        function showError(error) {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    document.getElementById('location').innerHTML = "Usuario denegó la solicitud de geolocalización.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    document.getElementById('location').innerHTML = "La información de ubicación no está disponible.";
                    break;
                case error.TIMEOUT:
                    document.getElementById('location').innerHTML = "La solicitud de geolocalización ha expirado.";
                    break;
                case error.UNKNOWN_ERROR:
                    document.getElementById('location').innerHTML = "Se ha producido un error desconocido.";
                    break;
            }
        }
    </script>
    <a href="logout.php" class="button">Cerrar sesión</a>
    <br>
    <?php
    require 'config.php';
    $username = $_SESSION['user_id']; 
    // Consulta SQL para obtener los datos
    $sql = "SELECT ciudad, temp_centigrados, desc_clima, fec_consulta FROM clima_user WHERE user_reg =".$username;
    // Preparar y ejecutar la consulta
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Obtener todos los resultados
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si hay resultados
    if (count($resultados) > 0) {
        // Iniciar la tabla HTML
        echo "<table>";
        echo "<tr><th>CIUDAD</th><th>TEMPERATURA</th><th>DESCRIPCION CLIMA</th><th>FECHA DE REGISTRO</th></tr>";

        // Iterar sobre los resultados y generar las filas de la tabla
        foreach ($resultados as $fila) {
            echo "<tr>";
            echo "<td>" . $fila['ciudad'] . "</td>";
            echo "<td>" . $fila['temp_centigrados'] . "</td>";
            echo "<td>" . $fila['desc_clima'] . "</td>";
            echo "<td>" . $fila['fec_consulta'] . "</td>";
            echo "</tr>";
        }

        // Cerrar la tabla HTML
        echo "</table>";
    } else {
        echo "No se encontraron resultados.";
    }

    // Cerrar la conexión
    $conn = null;
    ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
