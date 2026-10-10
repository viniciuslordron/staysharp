<?php 
    namespace app\Views;
    class Render {
        function render (string $view, array $params = []){
            // Irei tranformar cada item do array em uma variavel
            extract($params);
            ob_start();
            
            require __DIR__ . "/$view.php";

            //gravando o html da view na variavel conteúdo
            $conteudo = ob_get_clean();
            
            //gravando o layout agora
            ob_start();
            require __DIR__ . "/layout.php";
            return ob_get_clean();
        }
    }
?>