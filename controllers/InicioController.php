<?php

declare(strict_types=1);

final class InicioController extends Controller
{
    public function index(): void
    {
        if (Auth::check()) {
            $this->redirect(
                'index.php?controller=dashboard&action=index'
            );
        }

        $this->redirect('index.html');
    }
}