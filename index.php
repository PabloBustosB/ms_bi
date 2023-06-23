  <?php
  require 'vendor/autoload.php';
  Flight::register('db', 'PDO', array("pgsql:host=db;port=5432;dbname=db_hospitalbi", 'postgres', 'postgres'));
  Flight::route('/', function(){
      echo "hello world!";
  });
    Flight::route('GET /consultas', function () {
    $query = Flight::db()->prepare("SELECT * FROM consulta WHERE diagnostico is null order by id");
    $query->execute();
    $datos = $query->fetchAll(PDO::FETCH_ASSOC);
    Flight::json($datos);
  });

    Flight::route('GET /consultas/all', function () {
    $query = Flight::db()->prepare("SELECT * FROM consulta order by id");
    $query->execute();
    $datos = $query->fetchAll(PDO::FETCH_ASSOC);
    Flight::json($datos);
  });
  Flight::route('GET /consultas/doc/@doctor', function ($doctor) {
    $query = Flight::db()->prepare("SELECT id,fecha,hora,paciente FROM consulta WHERE doctor = $doctor and fecha = '23-06-2023' and diagnostico is null group by id");
    $query->execute();
    $datos = $query->fetchAll(PDO::FETCH_ASSOC);
    Flight::json($datos);
  });

    Flight::route('GET /consultas/pac/@paciente', function ($paciente) {
    $query = Flight::db()->prepare("SELECT con.id,con.doctor,con.paciente,con.fecha,con.hora,r.recomendacion,con.diagnostico,med.nombre,med.cantidad,med.indicacion FROM consulta as con,receta as r,medicamento as med WHERE r.idconsulta=con.id and med.idreceta=r.id and paciente = $paciente order by con.id");
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

    Flight::route('PUT /consultas/atencion/@id', function ($id) {
    $diagnostico = Flight::request()->data->diagnostico;
    $sql = "UPDATE consulta SET diagnostico=? WHERE id=$id";
    $query = Flight::db()->prepare($sql);
    $query->bindparam(1, $diagnostico);
    $query->execute();
    Flight::json(["Consulta Actualizada para un id"]);
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

    Flight::route('GET /recetas/cantidad', function () {
    $query = Flight::db()->prepare("SELECT count(*) as cantidad FROM receta");
    $query->execute();
    $datos = $query->fetchAll(PDO::FETCH_ASSOC);
    Flight::json($datos);
  });

  Flight::route('POST /recetas', function () {
    $recomendacion = Flight::request()->data->recomendacion;
    $idconsulta = Flight::request()->data->idconsulta;
    $sql = "INSERT INTO receta(recomendacion, idconsulta) VALUES (?,?);";
    $query = Flight::db()->prepare($sql);
    $query->bindparam(1, $recomendacion);
    $query->bindparam(2, $idconsulta);
    $query->execute();
    Flight::json(["Receta Registrada"]);
  });

  Flight::route('GET /medicamento', function () {
    $query = Flight::db()->prepare("SELECT * FROM medicamento");
    $query->execute();
    $datos = $query->fetchAll(PDO::FETCH_ASSOC);
    Flight::json($datos);
  });

  Flight::route('POST /medicamento', function () {
    $nombre = Flight::request()->data->nombre;
    $cantidad = Flight::request()->data->cantidad;
    $indicacion = Flight::request()->data->indicacion;
    $idreceta= Flight::request()->data->idreceta;
    $sql = "INSERT INTO medicamento(nombre, cantidad, indicacion, idreceta) VALUES (?,?,?,?);";
    $query = Flight::db()->prepare($sql);
    $query->bindparam(1, $nombre);
    $query->bindparam(2, $cantidad);
    $query->bindparam(3, $indicacion);
    $query->bindparam(4, $idreceta);
    $query->execute();
    Flight::json(["Medicamento Registrada"]);
  });

  Flight::route('GET /sala', function () {
    $query = Flight::db()->prepare("SELECT * FROM sala");
    $query->execute();
    $datos = $query->fetchAll(PDO::FETCH_ASSOC);
    Flight::json($datos);
  });

    Flight::route('GET /sala/libre', function () {
    $query = Flight::db()->prepare("SELECT * FROM sala WHERE estado = 'disponible'");
    $query->execute();
    $datos = $query->fetchAll(PDO::FETCH_ASSOC);
    Flight::json($datos);
  });

  Flight::route('POST /sala', function () {
    $estado = Flight::request()->data->estado;
    $sql = "INSERT INTO sala(estado) VALUES (?);";
    $query = Flight::db()->prepare($sql);
    $query->bindparam(1, $estado);
    $query->execute();
    Flight::json(["Sala Registrada"]);
  });

  Flight::route('PUT /sala', function () {
    $id = Flight::request()->data->id;
    $estado = Flight::request()->data->estado;
    $sql = "UPDATE sala SET estado=? WHERE id=?";
    $query = Flight::db()->prepare($sql);
    $query->bindparam(1, $estado);
    $query->bindparam(2, $id);
    $query->execute();
    Flight::json(["Sala Actualizada"]);
  });

  Flight::route('GET /internacion', function () {
    $query = Flight::db()->prepare("SELECT s.id,c.paciente,c.doctor,i.fechaini FROM internacion as i,consulta as c,sala as s WHERE i.idconsulta=c.id and i.idsala=s.id order by i.fechaini desc");
    $query->execute();
    $datos = $query->fetchAll(PDO::FETCH_ASSOC);
    Flight::json($datos);
  });

  Flight::route('POST /internacion', function () {
    $fechaini = Flight::request()->data->fechaini;
    $fechafin = Flight::request()->data->fechafin;
    $cantdias = Flight::request()->data->cantdias;
    $doctor = Flight::request()->data->doctor;
    $idsala = Flight::request()->data->idsala;
    $idconsulta = Flight::request()->data->idconsulta;

    $sql = "INSERT INTO internacion(fechaini,fechafin,cantdias,doctor,idsala,idconsulta) VALUES (?,?,?,?,?,?);";
    $query = Flight::db()->prepare($sql);
    $query->bindparam(1, $fechaini);
    $query->bindparam(2, $fechafin);
    $query->bindparam(3, $cantdias);
    $query->bindparam(4, $doctor);
    $query->bindparam(5, $idsala);
    $query->bindparam(6, $idconsulta);
    $query->execute();
    Flight::json(["internacion Registrada"]);
  });

  Flight::route('PUT /internacion', function () {
    $id = Flight::request()->data->id;
    $estado = Flight::request()->data->estado;
    $sql = "UPDATE sala SET estado=? WHERE id=?";
    $query = Flight::db()->prepare($sql);
    $query->bindparam(1, $estado);
    $query->bindparam(2, $id);
    $query->execute();
    Flight::json(["Sala Actualizada"]);
  });

  Flight::route('GET /kpi1', function () {
    $query = Flight::db()->prepare("SELECT nombre , count(nombre) as cantidad FROM medicamento group by nombre order by  count(nombre) desc LIMIT 4");
    $query->execute();
    $datos = $query->fetchAll(PDO::FETCH_ASSOC);
    Flight::json($datos);
  });
  Flight::route('GET /kpi2', function () {
    $query = Flight::db()->prepare("SELECT doctor , count (doctor) as cantpacientes from internacion group by doctor");
    $query->execute();
    $datos = $query->fetchAll(PDO::FETCH_ASSOC);
    Flight::json($datos);
  });
  Flight::route('GET /kpi3', function () {
    $query = Flight::db()->prepare("SELECT estado ,count(estado) as cantidad from sala group by estado");
    $query->execute();
    $datos = $query->fetchAll(PDO::FETCH_ASSOC);
    Flight::json($datos);
  });
  Flight::route('GET /kpi4', function () {
    $query = Flight::db()->prepare("SELECT paciente , count(paciente) as cantidadConsultas from consulta group by paciente");
    $query->execute();
    $datos = $query->fetchAll(PDO::FETCH_ASSOC);
    Flight::json($datos);
  });

  Flight::route('GET /kpi5', function () {
    $query = Flight::db()->prepare("SELECT doctor, count(doctor) from consulta group by doctor");
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
