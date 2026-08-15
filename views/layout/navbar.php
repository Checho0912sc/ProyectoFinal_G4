<?php

$usuarioNavegacion = Auth::usuario();

?>

<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container">

        <a
            class="navbar-brand"
            href="<?= e(url(
                'index.php?controller=dashboard&action=index'
            )) ?>"
        >
            <i class="bi bi-diagram-3-fill"></i>
            ComuniGest
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#menuPrincipal"
            aria-controls="menuPrincipal"
            aria-label="Abrir menú"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div
            class="offcanvas-lg offcanvas-end"
            tabindex="-1"
            id="menuPrincipal"
            aria-labelledby="menuPrincipalLabel"
        >
            <div class="offcanvas-header">

                <h5
                    class="offcanvas-title"
                    id="menuPrincipalLabel"
                >
                    <i class="bi bi-diagram-3-fill"></i>
                    ComuniGest
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="offcanvas"
                    data-bs-target="#menuPrincipal"
                    aria-label="Cerrar menú"
                ></button>

            </div>

            <div class="offcanvas-body">

                <ul
                    class="navbar-nav ms-auto
                    align-items-lg-center gap-lg-1"
                >
                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="<?= e(url(
                                'index.php?controller=dashboard'
                                . '&action=index'
                            )) ?>"
                        >
                            <i class="bi bi-speedometer2"></i>
                            Dashboard
                        </a>

                    </li>

                    <?php if (
                        Auth::tieneRol('Administrador')
                    ): ?>

                        <li class="nav-item">

                            <a
                                class="nav-link"
                                href="<?= e(url('usuario.html')) ?>"
                            >
                                <i class="bi bi-people"></i>
                                Usuarios
                            </a>

                        </li>

                    <?php endif; ?>

                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="<?= e(url('proyecto.html')) ?>"
                        >
                            <i class="bi bi-folder-check"></i>
                            Proyectos
                        </a>

                    </li>

                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="<?= e(url('index.php?controller=actividades&action=index')) ?>"
                        >
                            <i class="bi bi-calendar-event"></i>
                            Actividades
                        </a>

                    </li>

                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="<?= e(url('index.php?controller=grupos&action=index')) ?>"
                        >
                            <i class="bi bi-person-workspace"></i>
                            Grupos
                        </a>

                    </li>

                    <li class="nav-item dropdown ms-lg-2">

                        <button
                            class="btn btn-outline-success
                            dropdown-toggle btn-usuario"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <i class="bi bi-person-circle"></i>

                            <?= e(
                                $usuarioNavegacion['nombre']
                                ?? 'Cuenta'
                            ) ?>
                        </button>

                        <ul
                            class="dropdown-menu
                            dropdown-menu-end shadow-sm"
                        >
                            <li>
                                <div class="dropdown-item-text">

                                    <strong>
                                        <?= e(
                                            $usuarioNavegacion['rol']
                                            ?? ''
                                        ) ?>
                                    </strong>

                                    <small
                                        class="d-block text-muted"
                                    >
                                        <?= e(
                                            $usuarioNavegacion[
                                                'comunidad'
                                            ] ?? ''
                                        ) ?>
                                    </small>

                                </div>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>

                                <form
                                    action="<?= e(url(
                                        'index.php?controller=auth'
                                        . '&action=logout'
                                    )) ?>"
                                    method="post"
                                >
                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= e(
                                            Auth::csrfToken()
                                        ) ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="dropdown-item
                                        text-danger"
                                    >
                                        <i
                                            class="bi
                                            bi-box-arrow-right me-2"
                                        ></i>

                                        Cerrar sesión
                                    </button>
                                </form>

                            </li>
                        </ul>

                    </li>
                </ul>

            </div>
        </div>

    </div>
</nav>