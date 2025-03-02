<?php
class RungeKutta
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

    private function evaluarFuncion($x, $y)
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

        while ($x <= $xf) {
            $k1 = $h * $this->evaluarFuncion($x, $y);
            $k2 = $h * $this->evaluarFuncion($x + $h / 2, $y + $k1 / 2);
            $k3 = $h * $this->evaluarFuncion($x + $h / 2, $y + $k2 / 2);
            $k4 = $h * $this->evaluarFuncion($x + $h, $y + $k3);

            $y_next = $y + (1 / 6) * ($k1 + 2 * $k2 + 2 * $k3 + $k4);

            $resultados[] = [
                'n' => $n,
                'xn' => $x,
                'yn' => $y,
                'k1' => $k1,
                'k2' => $k2,
                'k3' => $k3,
                'k4' => $k4,
                'yn1' => $y_next
            ];

            $y = $y_next;
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

    $rungeKutta = new RungeKutta($x0, $y0, $h, $xf, $funcion);
    $resultados = $rungeKutta->calcular();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Método de Runge-Kutta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">
<a href="../" style="font-size: 2rem;">Regresar</a>

    <h2 class="mb-4">Método de Runge-Kutta</h2>
    <form action="" method="POST">
        <div class="row">
            <div class="col-md-2"><label>X₀:</label><input type="text" name="x0" class="form-control" placeholder="Ej: 1" required></div>
            <div class="col-md-2"><label>Y₀:</label><input type="text" name="y0" class="form-control" placeholder="Ej: 1" required></div>
            <div class="col-md-2"><label>h:</label><input type="text" name="h" class="form-control" placeholder="Ej: 0.1" required></div>
            <div class="col-md-2"><label>Xf:</label><input type="text" name="xf" class="form-control" placeholder="Ej: 2.0" required></div>
            <div class="col-md-4"><label>Función f(x,y):</label><input type="text" name="funcion" class="form-control" placeholder="Ej: x^3 - 4*y - 9" required></div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Calcular</button>
    </form>
    <?php if (isset($resultados)): ?>
        <h3 class="text-center mt-4">Resultados</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>N</th>
                    <th>Xn</th>
                    <th>Yn</th>
                    <th>K1</th>
                    <th>K2</th>
                    <th>K3</th>
                    <th>K4</th>
                    <th>Yn+1</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resultados as $resultado): ?>
                    <tr>
                        <td><?= $resultado['n'] ?></td>
                        <td><?= number_format($resultado['xn'], 5) ?></td>
                        <td><?= number_format($resultado['yn'], 5) ?></td>
                        <td><?= number_format($resultado['k1'], 5) ?></td>
                        <td><?= number_format($resultado['k2'], 5) ?></td>
                        <td><?= number_format($resultado['k3'], 5) ?></td>
                        <td><?= number_format($resultado['k4'], 5) ?></td>
                        <td><?= number_format($resultado['yn1'], 5) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>

</html>