/*==========================================
  🗃️ CREACIÓN DE LA BASE DE DATOS
==========================================*/
create database restaurante;
/*==========================================
  ⚙️ CONFIGURACIÓN GENERAL DEL SISTEMA
==========================================*/
CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `telefono` varchar(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `direccion` text NOT NULL,
  `mensaje` varchar(255) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*---------------------------------------------------------*/
ALTER TABLE `config` ADD PRIMARY KEY (`id`);

/*==========================================
  🛋️ GESTIÓN DE SALAS
==========================================*/
CREATE TABLE `salas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `mesas` int(11) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*==========================================
  🛋️ GESTIÓN DE MESAS
==========================================*/
CREATE TABLE `mesas` (
  `id_mesa` INT AUTO_INCREMENT PRIMARY KEY,
  `id_sala` INT NOT NULL,
  `num_mesa` INT NOT NULL,
  `capacidad` INT NOT NULL,
  `estado` ENUM('DISPONIBLE', 'OCUPADA', 'DESACTIVADA') DEFAULT 'DISPONIBLE',
  FOREIGN KEY (`id_sala`) REFERENCES `salas`(`id`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*==========================================
  🧾 GESTIÓN DE PEDIDOS
==========================================*/
CREATE TABLE pedidos (
  id int(11) NOT NULL AUTO_INCREMENT,
  id_sala int(11) NOT NULL,
  num_mesa int(11) NOT NULL,
  fecha timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  total decimal(10,2) NOT NULL,
  observacion text DEFAULT NULL,
  estado enum('ACTIVO','FINALIZADO') NOT NULL DEFAULT 'ACTIVO',
  id_usuario int(11) NOT NULL,
  ImpServicio int(25),
  tipoPago ENUM('efectivo', 'tarjeta') NOT NULL,
  PRIMARY KEY (id),
  KEY id_sala (id_sala)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
  ALTER TABLE pedidos
  ADD COLUMN transaccion VARCHAR(100) NULL AFTER tipoPago;

/*==========================================
  🧾 GESTIÓN DETALLE DE PEDIDOS
==========================================*/
CREATE TABLE detalle_pedidos (
  id int(11) NOT NULL AUTO_INCREMENT,
  nombre varchar(200) NOT NULL,
  precio decimal(10,2) NOT NULL,
  cantidad int(11) NOT NULL,
  id_pedido int(11) NOT NULL,
  estado enum('PENDIENTE', 'EN PREPARACION','LISTO PARA SERVIR', 'SERVIDO') NOT NULL DEFAULT 'PENDIENTE',
  tipo varchar(200) NOT NULL COMMENT '1 plato, 2 bebida',
  observacion text DEFAULT NULL,
  PRIMARY KEY (id),
  KEY id_pedido (id_pedido),
  CONSTRAINT detalle_pedidos_ibfk_1 FOREIGN KEY (id_pedido) REFERENCES pedidos (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*==========================================
  🍽️ PRODUCTOS: PLATOS
==========================================*/
CREATE TABLE `platos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(100) DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT NULL,
  `estado` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*==========================================
  🧾 GESTIÓN DE ORDENES LISTAS
==========================================*/
CREATE TABLE `ordenes_listas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `num_mesa` INT ,
  `nombre` VARCHAR(255) ,
  `cantidad` INT ,
  `fecha` datetime) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*==========================================
  🍽️ PRODUCTOS: BEBIDAS
==========================================*/
CREATE TABLE `bebidas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(100) DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT NULL,
  `estado` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*==========================================
  🧪 PEDIDOS TEMPORALES (CARRITO)
==========================================*/
CREATE TABLE `temp_pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cantidad` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `num_mesa` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `tipo` int(11) NOT NULL COMMENT '1 plato, 2 bebida',
  `observacion` text DEFAULT NULL,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*Correr este update en caso de que se hayan agregado datos*/
UPDATE temp_pedidos
SET observacion = ''
WHERE observacion IS NULL;
/*Sino se agregaron datos entonces correr este*/
ALTER TABLE temp_pedidos
MODIFY observacion VARCHAR(500) NOT NULL DEFAULT '';

/*==========================================
  🧍 TABLA DE USUARIOS DEL SISTEMA
==========================================*/
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `correo` varchar(200) NOT NULL,
  `pass` varchar(50) NOT NULL,
  `rol` int(11) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*Ejecutar este alter que hace falta para la tabla de Users*/
ALTER TABLE usuarios ADD COLUMN turno ENUM('diurno', 'nocturno') NOT NULL DEFAULT 'diurno';
ALTER TABLE usuarios 
ADD COLUMN pass_temp TINYINT(1) NOT NULL DEFAULT 1;


/*==========================================
  📦 PROVEEDORES
==========================================*/
CREATE TABLE proveedores (
    id_proveedor INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) UNIQUE NOT NULL);
    
/*==========================================
  📦 INVENTARIO
==========================================*/
CREATE TABLE inventario (
  id_inventario INT PRIMARY KEY AUTO_INCREMENT,
  id_bebida INT,
  cantidad DECIMAL(10,2) NOT NULL,
  precio Decimal (10,2) NOT NULL,
  total DECIMAL(10,2),
  iva DECIMAL(10,2) DEFAULT 0,
  total_final DECIMAL(10,2),
  id_proveedor int,
  fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor),
  FOREIGN KEY (id_bebida) REFERENCES bebidas(id));
  
/*==========================================
  📊 REPORTE FINANCIERO
==========================================*/
CREATE TABLE repo_financiero (
    id_reporte INT PRIMARY KEY AUTO_INCREMENT,
    Fecha DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,  
    inventario_inicial DECIMAL(12,2) NOT NULL, 
    compras DECIMAL(12,2) NOT NULL,
    gastos_mes DECIMAL(12,2) NOT NULL,
    salarios DECIMAL(12,2) NOT NULL,
    impuestos DECIMAL(12,2) NOT NULL,
    cuentas_por_pagar DECIMAL(12,2) NOT NULL,
    total_salidas DECIMAL(12,2) GENERATED ALWAYS AS (inventario_inicial + compras + gastos_mes + salarios + impuestos + cuentas_por_pagar) VIRTUAL, 
    ventas DECIMAL(12,2) NOT NULL,
    inventario_final DECIMAL(12,2),
    total_entradas DECIMAL(12,2) GENERATED ALWAYS AS (ventas + inventario_final) VIRTUAL,
    utilidades DECIMAL(12,2) GENERATED ALWAYS AS (total_entradas - total_salidas) VIRTUAL);

/*--------------------------------------------------------------------------------------------------------------------------------------------------------*/

/*==========================================
  🧪 DATOS DE PRUEBA INICIALES
==========================================*/
INSERT INTO `config` (`id`, `nombre`, `telefono`, `email`, `direccion`, `mensaje`) VALUES
(1, 'Restaurante San Isidro', '89377531', 'mespinozacam@yahoo.es', 'Puriscal - San José', '¡Realizado con Exito!');
/*---------------------------------------------------------------------------------------------------------------------*/
INSERT INTO `platos` (`id`, `nombre`, `precio`, `imagen`, `fecha`, `estado`) VALUES
(1, 'AJI DE GALLINA', '10.00', '', NULL, 1),
(2, 'CEVICHE', '25.00', '', NULL, 1),
(3, 'ARROZ CON POLLO', '8.00', '', NULL, 1);
/*---------------------------------------------------------------------------------------------------------------------*/
INSERT INTO `salas` (`id`, `nombre`, `mesas`, `estado`) VALUES
(1, 'ENTRADA PRINCIPAL', 5, 1),
(2, 'SEGUNDO PISO', 10, 1),
(3, 'FRENTE COCINA', 8, 1);
/*---------------------------------------------------------------------------------------------------------------------*/
  INSERT INTO `bebidas` (`id`, `nombre`, `precio`, `fecha`, `imagen`) VALUES
(1, 'AguaL', 500, '2023-05-25 20:03:27', '../assets/img/bebidas/20250220071401.jpg'),
(2, 'Coca', 500, '2023-05-25 20:03:27', '../assets/img/bebidas/20250210041557.jpg'),
(3, 'Pepsi', 500, '2023-05-25 20:03:27', '../assets/img/bebidas/Pepsi.jpg');
/*---------------------------------------------------------------------------------------------------------------------*/
INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `pass`, `rol`, `estado`) VALUES
(1, 'Owner', 'OwnerAcces@gmail.com', 'aedf1a85066fbbfe8ff6dfe465ff772f', 1, 1);
/*---------------------------------------------------------------------------------------------------------------------*/
INSERT INTO `mesas` (`id_sala`, `num_mesa`, `capacidad`, `estado`) VALUES
(1, 1, 4, 'DISPONIBLE');
/*---------------------------------------------------------------------------------------------------------------------*/


