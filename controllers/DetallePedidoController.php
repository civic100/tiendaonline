<?php 
    class DetallePedidoController{
        public static function añadirCarrito(){
            if(isset($_GET['isbn'])){
                //unset($_SESSION['carrito']);
                $isbn=intval($_GET['isbn']);
                $lista = array();
                if(isset($_SESSION['carrito'][$isbn])){ 
                    require_once "models/producto.php";
                    $producto = new Producto();
                    $producto->setIsbn($isbn);
                    $stock = $producto->obtenerStock();
                    if($_SESSION['carrito'][$isbn] > $stock[0]->stock){
                        $_SESSION['carrito'][$isbn]++;
                    }
                }else{
                    require_once "models/producto.php";
                    require_once "models/detallePedido.php";
                    $detallePedido = new DetallePedido();
                    $detallePedido->setIsbn($isbn);
                    $detallePedido->setUnidades($_GET['cantidad']);
                    $detallePedido->setIdDetallePedido($_GET['cantidad']);
                    $producto = new Producto();
                    $producto->setIsbn($isbn);
                    if($producto->listadoProducto()){
                        $stock = $producto->obtenerStock();
                        if($stock[0]->stock > 1){
                            $_SESSION['carrito'][$detallePedido->getISBN()] = $detallePedido->getUnidades();
                        }else{
                            ?>
                            <script>alert('No hay stock del producto')</script>
                            <?php
                        }
                    }else{
                        ?>
                        <script>alert('No existe el producto')</script>
                        <?php
                    }
                }
            }else{
                ?>
                    <script>alert('No existe el producto')</script>
                <?php
                
            }
            if(isset($_GET['comprar']) && $_GET['comprar'] == 'si'){
                ?>
                <script>window.location.replace("index.php?controller=DetallePedido&action=listarCarrito");</script>
                <?php
            }else{
                ?>
                <script>window.location.replace("index.php");</script>
                <?php
            }
        }

        public static function vaciarCarrito(){
            unset($_SESSION['carrito']);
        }

        public static function listarCarrito(){
            require_once "models/producto.php";
            require_once "models/detallePedido.php";
            if(isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0){
                foreach($_SESSION['carrito'] as $clave => $valor){
                    $listaIsbn[] = $clave;
                }
                $producto = new Producto();
                $listadoCarrito = $producto->listadoCarrito($listaIsbn);
                foreach($listadoCarrito as $clave => $valor){
                    $listado[$valor->ISBN] = $valor;
                }
                if(array_key_exists('button1', $_POST)) {
                    if(intval($listado[$_POST['isbn']]->stock) > intval($_SESSION['carrito'][$_POST['isbn']])){
                        $_SESSION['carrito'][$_POST['isbn']]++;
                    }
                    ?>
                    <script>window.location.replace("index.php?controller=DetallePedido&action=listarCarrito");</script>
                    <?php
        
                }
                else if(array_key_exists('button2', $_POST)) {
                    if($_SESSION['carrito'][$_POST['isbn']] > 1){
                        $_SESSION['carrito'][$_POST['isbn']]--;
                    }
                    ?>
                    <script>window.location.replace("index.php?controller=DetallePedido&action=listarCarrito");</script>
                    <?php
                }else if(array_key_exists('button3', $_POST)) {
                    unset($_SESSION['carrito'][$_POST['isbn']]);
                    ?>
                    <script>window.location.replace("index.php?controller=DetallePedido&action=listarCarrito");</script>
                    <?php
                }else if(array_key_exists('button4', $_POST)) {
                    DetallePedidoController::vaciarCarrito();
                    ?>
                    <script>window.location.replace("index.php?controller=DetallePedido&action=listarCarrito");</script>
                    <?php
                }else if(array_key_exists('cantidad', $_POST)) {
                    $_SESSION['carrito'][$_POST['isbn']] = intval($_POST['cantidad']);
                    ?>
                    <script>window.location.replace("index.php?controller=DetallePedido&action=listarCarrito");</script>
                    <?php
                }

                //Calcular Subtotal SIN IVA
                $total = 0;
                foreach ($listadoCarrito as $clave => $valor) {
                    $total += $valor->precio*intval($_SESSION['carrito'][$valor->ISBN]);
                }
                $subtotal = $total-($total*0.04);
                $iva = $total*0.04;
            }
            require_once "views/producto/carrito.php";
        }

        
    }
?>