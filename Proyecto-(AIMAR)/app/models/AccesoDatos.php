<?php

/*
 * Acceso a datos con BD Usuarios : 
 * Usando la librería mysqli
 * Uso el Patrón Singleton :Un único objeto para la clase
 * Constructor privado, y métodos estáticos 
 */
class AccesoDatos {
    
    private static $modelo = null;
    private $dbh = null;
    
    public static function getModelo(){
        if (self::$modelo == null){
            self::$modelo = new AccesoDatos();
        }
        return self::$modelo;
    }
    
    // Constructor privado  Patron singleton
    private function __construct(){
        $this->dbh = new mysqli(DB_SERVER, DB_USER, DB_PASSWD, DATABASE);
        if ($this->dbh->connect_error){
            die("Error en la conexión " . $this->dbh->connect_errno);
        } else {
            echo "Conexión exitosa";
        }
    }
    public function __clone() { 
        trigger_error('La clonación no permitida', E_USER_ERROR); 
    }

    // SELECT Devuelvo la lista de Usuarios
    public function getClientes ($primero,$cuantos):array {
        $tuser = [];
        $stmt_usuarios  = $this->dbh->prepare("select * from Clientes limit $primero,$cuantos");
        if ( $stmt_usuarios == false) die (__FILE__.':'.__LINE__.$this->dbh->error);
        $stmt_usuarios->execute();
        $result = $stmt_usuarios->get_result();
        if ( $result ){
            while ( $user = $result->fetch_object('Cliente') ){
               $tuser[]= $user;
            }
        }
        return $tuser;
    }
    
    // SELECT Devuelvo un usuario o false
    public function getCliente (int $id) {
        $cli = false;
        $stmt_usuario   = $this->dbh->prepare("select * from Clientes where id =?");
        if ( $stmt_usuario == false) die ($this->dbh->error);
        $stmt_usuario->bind_param("i",$id);
        $stmt_usuario->execute();
        $result = $stmt_usuario->get_result();
        if ( $result ){
            $cli = $result->fetch_object('Cliente');
        }
        return $cli;
    }

    // SELECT Devuelvo el siguiente cliente o false
    public function getClienteSiguiente($id){
        $cli = false;
        $stmt_usuario = $this->dbh->prepare("select * from Clientes where id > ? limit 1");
        if ($stmt_usuario == false) die ($this->dbh->error);
        $stmt_usuario->bind_param("i", $id);
        $stmt_usuario->execute();
        $result = $stmt_usuario->get_result();
        if ($result) {
            $cli = $result->fetch_object('Cliente');
        }
        return $cli;
    }

    // SELECT Devuelvo el cliente anterior o false
    public function getClienteAnterior($id){
        $cli = false;
        $stmt_usuario = $this->dbh->prepare("select * from Clientes where id < ? order by id DESC limit 1");
        if ($stmt_usuario == false) die ($this->dbh->error);
        $stmt_usuario->bind_param("i", $id);
        $stmt_usuario->execute();
        $result = $stmt_usuario->get_result();
        if ($result) {
            $cli = $result->fetch_object('Cliente');
        }
        return $cli;
    }

    // Verifica si el correo electrónico ya existe
    public function emailExists($email, $id = null) {
        $stmt = $this->dbh->prepare("SELECT id FROM Clientes WHERE email = ?" . ($id ? " AND id != ?" : ""));
        if ($id) {
            $stmt->bind_param("si", $email, $id);
        } else {
            $stmt->bind_param("s", $email);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // Añadir nuevo cliente
    public function addCliente($datos) {
        $stmt = $this->dbh->prepare("INSERT INTO Clientes (first_name, last_name, email, gender, ip_address, telefono, imagen) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $datos['first_name'], $datos['last_name'], $datos['email'], $datos['gender'], $datos['ip_address'], $datos['telefono'], $datos['imagen']);
        $stmt->execute();
    }

    // Modificar cliente existente
    public function modCliente($id, $datos) {
        $stmt = $this->dbh->prepare("UPDATE Clientes SET first_name = ?, last_name = ?, email = ?, gender = ?, ip_address = ?, telefono = ?, imagen = ? WHERE id = ?");
        $stmt->bind_param("sssssssi", $datos['first_name'], $datos['last_name'], $datos['email'], $datos['gender'], $datos['ip_address'], $datos['telefono'], $datos['imagen'], $id);
        $stmt->execute();
    }

    // Cierro la conexión anulando todos los objectos relacioanado con la conexión PDO (stmt)
    public static function closeModelo(){
        if (self::$modelo != null){
            $obj = self::$modelo;
            $obj->dbh->close();
            self::$modelo = null;
        }
    }

public function getClientesOrdenados($orden = 'id'): array {
    $tuser = [];
    $allowedColumns = ['id', 'first_name', 'last_name', 'email', 'gender', 'ip_address'];
    if (!in_array($orden, $allowedColumns)) {
        $orden = 'id';
    }
    $stmt_usuarios = $this->dbh->prepare("SELECT * FROM Clientes ORDER BY $orden");
    if ($stmt_usuarios == false) die (__FILE__.':'.__LINE__.$this->dbh->error);
    $stmt_usuarios->execute();
    $result = $stmt_usuarios->get_result();
    if ($result) {
        while ($user = $result->fetch_object('Cliente')) {
            $tuser[] = $user;
        }
    }
    return $tuser;
}



// Devuelve el número total de clientes
public function numClientes() {
    $stmt = $this->dbh->prepare("SELECT COUNT(*) as total FROM Clientes");
    if ($stmt == false) die ($this->dbh->error);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'];
}

}