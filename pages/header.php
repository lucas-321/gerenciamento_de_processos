<?php
    session_start();

    $categoria = $_SESSION['categoria'];

    switch ($categoria) {
        case 1:
            $n_categoria = "Administrador";
            break;
        case 2:
            $n_categoria = "Coordenador";
            break;
        case 3:
            $n_categoria = "Protocolo";
            break;
        case 4:
            $n_categoria = "Analista";
            break;
        case 5:
            $n_categoria = "Externo";
            break;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/lists.css">
    <link rel="stylesheet" href="../css/filter.css">
</head>
<body>

    <nav>
        <ul>
            <span class="logoHome">
                <li class="logo nav-item">
                    <a href="dashboard.php">
                        <img src="../img/logo-no-bg.png" alt="img">
                        <span>Projeto Preliminar</span>
                    </a>
                </li>
            </span>

            <span class="links">

                <?php
                    if ($_SESSION["categoria"] == 1 || $_SESSION["categoria"] == 2) {
                ?>
                        <li class="nav-item">
                            <a href='painel.php'>Usuários</a>
                        </li>
                        <!-- <li class="nav-item">
                            <a href='lista_processos.php'>Processos</a>
                        </li> -->
                        <!-- Processos -->
                        <li class="nav-item nav-list" onclick="toggleSubmenu(this)">
                            <span><a href='#'>Processos</a></span>
                            <ul>

                                <li class="subitem">
                                    <a href='lista_processos.php'>Geral</a>
                                </li>
                                <li class="subitem">
                                    <a href='painel_analista.php'>Meus Processos</a>
                                </li>

                            </ul>
                        </li>
                        <!-- Fim -->
                        <li class="nav-item">
                            <a href='lista_assuntos.php'>Assuntos</a>
                        </li>

                        <!-- Destinos -->
                        <li class="nav-item nav-list" onclick="toggleSubmenu(this)">
                            <span><a href='#'>Destinos</a></span>
                            <ul>

                                <li class="subitem">
                                    <a href='lista_setores.php'>Setores</a>
                                </li>
                                <li class="subitem">
                                    <a href='lista_pastas.php'>Pastas</a>
                                </li>

                            </ul>
                        </li>
                        <!-- Fim -->
                         
                        <!-- <li class="nav-item nav-list" onclick="toggleSubmenu(this)">
                            <span><a href='#'>Relatórios</a></span>
                            <ul>
                                <li class="subitem"><a href="relatorios.php">Processos</a></li>
                                <li class="subitem"><a href="relatorios.php">Usuários</a></li>
                            </ul>
                        </li> -->

                        <!-- <li class="nav-item">
                            <a href='relatorios.php'>Relatório</a>
                        </li> -->

                        <!-- Registros -->
                        <li class="nav-item nav-list" onclick="toggleSubmenu(this)">
                            <span><a href='#'>Registros</a></span>
                            <ul>

                                <li class="subitem">
                                    <a href='relatorios.php'>Processos</a>
                                </li>
                                <li class="subitem">
                                    <a href='registros.php'>Atividades</a>
                                </li>

                            </ul>
                        </li>
                        <!-- Fim -->
                <?php
                    }else if ($_SESSION["categoria"] == 3) {
                ?>

                <li class="nav-item">
                    <a href="lista_processos.php">Processos</a>
                </li>

                <li class="nav-item nav-list" onclick="toggleSubmenu(this)">
                    <span><a href='#'>Relatórios</a></span>
                    <ul>
                        <li class="subitem"><a href="relatorios.php">Processos</a></li>
                    </ul>
                </li>

                <?php
                    }else if($_SESSION["categoria"] == 4){
                ?>

                <li class="nav-item">
                    <a href="painel_analista.php">Meus Processos</a>
                </li>

                <li class="nav-item nav-list" onclick="toggleSubmenu(this)">
                    <span><a href='#'>Relatórios</a></span>
                    <ul>
                        <li class="subitem"><a href="relatorios.php">Processos</a></li>
                    </ul>
                </li>

                <?php
                    }
                ?>
            </span>

            <span class="profile menu-button">
                <li class="nav-item  nav-list"  onclick="toggleSubmenu(this)">
                    <a href="#">
                    <div class="profile-header">
                        <div class='item-list-header'>
                            <img src='../fotos_perfil/<?php echo "$_SESSION[foto]"; ?>' alt='img-perfil'>
                        </div>
                        <div class="profile-data">
                            <?php
                                $nome = strstr($_SESSION['nome'], ' ', true);
                                echo $nome;
                            ?>
                            <span class="profile-category"><?php echo "$n_categoria"; ?></span>
                        </div>
                    </div>
                    </a>

                    <ul>
                        <?php if ($_SESSION["categoria"] == 1 || $_SESSION["categoria"] == 2 ) { ?>
                            <li class="subitem"><a href="meus_dados.php">Meus Dados</a></li>
                        <?php } ?>
                        <li class="subitem"><a href="../logout.php">Sair</a></li>
                    </ul>

                </li>

            </span>
            
        </ul>
    </nav>

<script>





    function toggleSubmenu(element) {
        var submenu = element.querySelector('ul');
        submenu.classList.toggle('active');
    }

    document.addEventListener('click', function(event) {
        var submenus = document.querySelectorAll('.nav-list ul.active');
        for (var i = 0; i < submenus.length; i++) {
            if (!submenus[i].parentNode.contains(event.target)) {
                submenus[i].classList.remove('active');
            }
        }
    });
</script>

</body>
</html>