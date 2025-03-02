<?php
class EulerMejorado
{
    private $x0, $y0, $h, $xf, $funcion;

    public function __construct($x0, $y0, $h, $xf, $funcion)
    {
        $this->x0 = $x0;
        $this->y0 = $y0;
        $this->h = $h;
        $this->xf = $xf;
        $this->funcion = $funcion;
    }

    public function evaluarFuncion($x, $y)
    {
        $funcion = str_replace(["x", "y"], ["\$x", "\$y"], $this->funcion);
        $resultado = 0;
        eval("\$resultado = $funcion;");
        return $resultado;
    }

    public function calcular()
    {
        $x = $this->x0;
        $y = $this->y0;
        $h = $this->h;
        $xf = $this->xf;

        $resultados = [];
        $n = 0;

        // Guardamos el primer valor de yn para el error absoluto
        $first_y = $y;

        // Primero agregamos el valor inicial (y0) a la tabla
        $yr = exp(-0.2 + 0.2 * pow($x, 2));  // Cálculo de yr con x0
        $error_absoluto = abs($yr - $first_y);

        $resultados[] = [
            'n' => $n,
            'xn' => $x,
            'yn' => $y,
            'yr' => $yr,
            'error_absoluto' => $error_absoluto
        ];

        $x += $h;
        $n++;

        while ($x <= $xf) {
            $f_xy = $this->evaluarFuncion($x, $y);
            $y_pred = $y + $h * $f_xy;
            $f_xy_pred = $this->evaluarFuncion($x + $h, $y_pred);
            $y_corr = $y + ($h / 2) * ($f_xy + $f_xy_pred);

            // Calculamos el valor real (yr) usando la fórmula proporcionada
            $yr = exp(-0.2 + 0.2 * pow($x, 2));

            // Calculamos el error absoluto como la diferencia entre yr y yn
            $error_absoluto = abs($yr - $y_corr);

            $resultados[] = [
                'n' => $n,
                'xn' => $x,
                'yn' => $y_corr,
                'yr' => $yr,
                'error_absoluto' => $error_absoluto
            ];

            $y = $y_corr;
            $x += $h;
            $n++;
        }

        return $resultados;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $x0 = floatval($_POST['x0']);
    $y0 = floatval($_POST['y0']);
    $h = floatval($_POST['h']);
    $xf = floatval($_POST['xf']);
    $funcion = $_POST['funcion'];

    // Instanciamos la clase y calculamos los resultados
    $euler = new EulerMejorado($x0, $y0, $h, $xf, $funcion);
    $resultados = $euler->calcular();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euler Mejorado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 900px;
        }

        h2 {
            font-size: 28px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            margin-top: 20px;
        }

        th {
            background-color: #007bff;
            color: white;
            font-size: 16px;
        }

        td {
            text-align: center;
            vertical-align: middle;
        }

        .form-control {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
        }

        .btn {
            width: 100%;
            padding: 12px;
            font-size: 18px;
        }

        .form-group {
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="container mt-4">
<a href="../" style="font-size: 2rem;">Regresar</a>

    <h2 class="mb-4">Método de Euler Mejorado</h2>

    <!-- Formulario para ingresar datos -->
    <form action="" method="POST" class="mb-4">
        <div class="row">
            <div class="col-md-2">
                <label for="x0">X₀:</label>
                <input type="text" name="x0" class="form-control" placeholder="Ej: 1" required>
            </div>
            <div class="col-md-2">
                <label for="y0">Y₀:</label>
                <input type="text" name="y0" class="form-control" placeholder="Ej: 1" required>
            </div>
            <div class="col-md-2">
                <label for="h">h (Paso):</label>
                <input type="text" name="h" class="form-control" placeholder="Ej: 0.1" required>
            </div>
            <div class="col-md-2">
                <label for="xf">Xf (Final):</label>
                <input type="text" name="xf" class="form-control" placeholder="Ej: 2.0" required>
            </div>
            <div class="col-md-4">
                <label for="funcion">Función f(x,y):</label>
                <input type="text" name="funcion" class="form-control" placeholder="Ej: x^3 - 4*y - 9" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Calcular</button>
    </form>

    <!-- Mostrar los resultados si existen -->
    <?php if (isset($resultados)): ?>
        <h3 class="text-center mb-4">Resultados</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">N</th>
                    <th scope="col">xn</th>
                    <th scope="col">yn (Euler Mejorado)</th>
                    <th scope="col">yr (Valor Real)</th>
                    <th scope="col">Error Absoluto</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resultados as $resultado): ?>
                    <tr>
                        <td><?= $resultado['n'] ?></td>
                        <td><?= number_format($resultado['xn'], 5) ?></td>
                        <td><?= number_format($resultado['yn'], 5) ?></td>
                        <td><?= number_format($resultado['yr'], 5) ?></td>
                        <td><?= number_format($resultado['error_absoluto'], 5) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>

</body>

</html>