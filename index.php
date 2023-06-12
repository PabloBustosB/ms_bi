  <?php
  require 'vendor/autoload.php';
  Flight::register('db', 'PDO', array("pgsql:host=localhost;port=5432;dbname=db_hospitalbi", 'postgres', 'postgres'));

  Flight::route('/', function(){
      echo 'hello world!';
  });
  
  Flight::route('GET /consultas', function () {
    $query = Flight::db()->prepare("SELECT * FROM consulta");
    $query->execute();
    $datos = $query->fetchAll(PDO::FETCH_ASSOC);
    Flight::json($datos);
  });

  Flight::route('POST /consultas', function () {
    $fecha = Flight::request()->data->fecha;
    $hora = Flight::request()->data->hora;
    $diagnostico = Flight::request()->data->diagnostico;
    $doctor = Flight::request()->data->doctor;
    $paciente = Flight::request()->data->paciente;

    $sql = "INSERT INTO consulta(fecha, hora, diagnostico, doctor, paciente)
    VALUES (?,?,?,?,?);";
    $query = Flight::db()->prepare($sql);
    $query->bindparam(1, $fecha);
    $query->bindparam(2, $hora);
    $query->bindparam(3, $diagnostico);
    $query->bindparam(4, $doctor);
    $query->bindparam(5, $paciente);
    $query->execute();
    Flight::json(["Consulta Registrada"]);
  });
  
  Flight::route('DELETE /consultas', function () {
    $id = Flight::request()->data->id;
    $sql = "DELETE FROM consulta WHERE  id=?";
    $query = Flight::db()->prepare($sql);
    $query->bindParam(1, $id);
    $query->execute();
    Flight::json(["Consulta Borrada"]);
  });
  
  Flight::route('PUT /consultas', function () {
    $id = Flight::request()->data->id;
    $fecha = Flight::request()->data->fecha;
    $hora = Flight::request()->data->hora;
    $diagnostico = Flight::request()->data->diagnostico;
    $doctor = Flight::request()->data->doctor;
    $paciente = Flight::request()->data->paciente;
  
    $sql = "UPDATE consulta SET fecha=?, hora=?, diagnostico=?, doctor=?, paciente=? WHERE id=?";
    $query = Flight::db()->prepare($sql);
    $query->bindparam(1, $fecha);
    $query->bindparam(2, $hora);
    $query->bindparam(3, $diagnostico);
    $query->bindparam(4, $doctor);
    $query->bindparam(5, $paciente);
    $query->bindparam(6, $id);
    $query->execute();
    Flight::json(["Consulta Actualizada"]);
  });

  Flight::route('GET /recetas', function () {
    $query = Flight::db()->prepare("SELECT * FROM receta");
    $query->execute();
    $datos = $query->fetchAll(PDO::FETCH_ASSOC);
    Flight::json($datos);
  });

Flight::before('json', function () {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE');
    header('Access-Control-Allow-Headers: Content-Type');
  });

Flight::start();