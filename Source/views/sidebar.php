<div class = 'sidebar__profile'>
    <img src = "<?= url('/Source/assets/img/user.png');?>">
    <p>Bem vindo, <?= $_SESSION["user"]->name ?></p>
</div>
<ul class = 'sidebar__menu'>
    <?php if ($_SESSION["user"]->type) :?>
        <li><a href = '<?= url("app/usuarios"); ?>'><i class="icon ion-md-people"></i> Usuários </a></li>
        <li><a href = '<?= url("app/cadastros"); ?>'><i class="icon ion-md-filing"></i> Cadastro</a></li>
        <li><a href = '<?= url("app/relatorios"); ?>'><i class="icon ion-md-podium"></i> Relatórios</a></li>
        <li><a href = '<?= url("app/lancamentos"); ?>'><i class="icon ion-md-clipboard"></i> Lançamentos</a></li>
        <li class='logout'><a><i class="icon ion-md-log-out"></i>Sair</a></li>
    <?php else: ?>
        <li><a href='<?= url("app/lancamentos"); ?>'><i class="icon ion-md-clipboard"></i> Lançamentos</a></li>
        <li class='logout'><a><i class="icon ion-md-log-out"></i>Sair</a></li>
    <?php endif; ?>
</ul>

<?php
    $v->start("sidebar-script");
?>
    <script>
        const logout = document.querySelectorAll('.logout');

        logout.forEach(el => {
            el.addEventListener('click', () => {
                window.localStorage.removeItem("amount");
                window.localStorage.removeItem("firstIncome");
                window.localStorage.removeItem("shift");
                window.location.href = '<?= url("logout");?>';
            });
        });

    </script>
<?php
    $v->end();
?>