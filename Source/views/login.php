<?php
    $v->layout("_app");
?>

    <div class = 'login'>
        <form class = 'login__form' method = 'POST' action = '<?= url("login"); ?>'>
            <h2>Login</h2>
            <div class = 'input-container'>
                <label for = "user"><i class="icon ion-md-person"></i> Usuário</label>
                <input type = "text" name = "user" required>
            </div>
            <div class = 'input-container'>
                <label for = "password"><i class="icon ion-md-lock"></i> Senha</label>
                <input type = "password" name = "password" required>
            </div>
            <div class = 'input-container'>
                <?php
                    if (isset($success)) {
                        if (!$success) {
                            echo "<p class = 'error-login'>O usuário ou a senha informados não estão corretos.</p>";
                        }
                    }
                ?>
            </div>
            <input type = "submit" value = "Entrar" class = "submit-password">
        </form>
        <div class = 'powered-by'>
            <img src = '<?= url("Source/assets/img/min-index-logo.png")?>' alt = 'Index Desenvolvimentos'>
            <p>Desenvolvido por Index Desenvolvimentos &#169;. Todos os direitos reservados.</p>
        </div>
    </div>