<?php 
    namespace App\Controllers;
    use App\Views\Render;
    class HomeController {
        public function index(): string {
            $titulo = "StaySharp";
            return (new Render()) -> render (
                'home/index',
                compact('titulo')
            );
        }
    }
?>