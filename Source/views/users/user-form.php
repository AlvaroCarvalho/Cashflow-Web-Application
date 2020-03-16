<?php
    $v->layout("_app");
?>
<form class = 'user-form' method = 'POST' data-action = '<?= url("app/users/create"); ?>' enctype = 'multpart/form-data'>
    <h2>Adicionar novo usuário</h2>
    <label class = 'input-container' for = 'name'>
        Nome
        <input required type = 'text' name = 'name' id  = 'name' placeholder = 'Nome'/>
    </label>
    <label class = 'input-container' for = 'username'>
        Nome de usuário
        <input required type = 'text' name = 'username' id = 'username' placeholder = 'Nome de usuário'/>
    </label>
    <label class = 'input-container' for = 'type'>
        Tipo de usuário
        <select name = 'type' id = 'type'>
            <option value = 0 selected>Operador</option>
            <option value = 1>Administrador</option>
        </select>
    </label>
    <label class = 'input-container' for = 'password'>
        Senha
        <input required type = 'password' name = 'password' id = 'password' placeholder = 'Senha'/>
    </label>
    <label class = 'input-container'>
        <img class = 'loading'src = '<?= url("Source/assets/img/load.gif"); ?>'>
        <input type = 'submit' value = 'Adicionar' id = 'create-user'/>
        <input type = 'submit' value = 'Atualizar' id = 'update-user'/>
    </label>
</form>
<div class="users-list">
    <h2>Usuários</h2>
    <div class = 'users-list__list'>
        <ul class = 'users-list__user users-list__summary'>
            <li>ID</li>
            <li>Nome</li>
            <li>Tipo</li>
            <li>Status</li>
            <li>Editar</li>
            <li>Apagar</li>
        </ul>
        <?php
            if ($users) :
                foreach ($users as $user) :
        ?>
        <ul class = 'users-list__user'>
            <li><?= $user->id; ?></li>
            <li><?= $user->name; ?></li>
            <li><?= $user->type ? "Administrador" : "Operador"; ?></li>
            <li>
                <select data-action = "<?= url("app/users/{$user->id}/status");?>" class = "update-user-status">
                    <option value = "1" <?= $user->status ? "selected" : ""; ?>>Ativo</option>
                    <option value = "0" <?= $user->status ? "" : "selected"; ?>>Inativo</option>
                </select>
            </li>
            <li><i data-action = "<?= url('app/users/update') ?>" data-id = "<?= $user->id; ?>" data-name = "<?= $user->name; ?>" data-user-name = "<?= $user->username; ?>" data-password = "<?= $user->password; ?>" data-type = "<?= $user->type; ?>" class="update-user icon ion-md-create"></i></li>
            <li><i data-action = "<?= url('app/users/delete/' . $user->id) ?>" class="destroy-user icon ion-md-trash"></i></li>
        </ul>
        <?php
                endforeach;
            else :
        ?>
                <p class = 'no-records'>Nenhum usuário cadastrado!</p>
        <?php
            endif;
        ?>            
    </div>
</div>

<?php
    $v->start("script");
?>
    <script>

        /**=== Add New User ===**/

        const createButton = document.getElementById("create-user");
        const userList = document.querySelector(".users-list__list");
        const form = document.querySelector(".user-form");

        const getData = () => {
            const data = {
                username: document.getElementById("username").value,
                password: document.getElementById("password").value,
                name: document.getElementById("name").value,
                type: document.getElementById("type").value
            };

            return data;
        };

        const sendData = async data => {
            const url = form.dataset.action;

            const request = await fetch(url, {
                method: 'POST',
                headers: {
                    'content-type': 'application/json',
                    'accept': 'application/json'
                },
                body: data
            });

            const response = await request.json();

            return response;
        };


        createButton.addEventListener("click", async e => {
            e.preventDefault();

            let data = getData();
            data = JSON.stringify(data);

            disableForm(form);
            showLoading(createButton);

            const response = await sendData(data);

            removeMessage();
            enableForm(form);
            hideLoading(createButton, 'block');

            createButton.insertAdjacentHTML("beforebegin", response.message);
        });

        /*=== Destroy user ===*/

        const deleteButton = document.querySelectorAll('.destroy-user');

        const sendDeleteData = async el => {
            const action = el.dataset.action;

            const request = await fetch(action, {
                method: 'DELETE',
                headers: {
                    'accept': 'application/json'
                }
            });

            const response = await request.json();
            return response;
        };

        deleteButton.forEach(el => el.addEventListener('click', async () => {
            
            const response = await sendDeleteData(el);

            if (response.success) {
                const user = el.parentElement.parentElement;

                user.classList.add("fadeOut");
                setTimeout(() => user.parentElement.removeChild(user), 500);                
            }
        }));

        /*=== Update user ===*/

        const fillButtons = document.querySelectorAll(".update-user");
        const updateButton = document.getElementById("update-user");

        const fillFormFields = el => {
            document.getElementById("username").value = el.dataset.userName;
            document.getElementById("password").value = el.dataset.password;
            document.getElementById("name").value = el.dataset.name;
            document.getElementById("create-user").style.display = 'none';
            updateButton.style.display = 'block';
            updateButton.setAttribute("data-action", el.dataset.action);
            updateButton.setAttribute("data-id", el.dataset.id);
        };

        const updateUser = async data => {
            const url = updateButton.dataset.action;

            const request = await fetch(url, {
                method: 'PUT',
                headers: {
                    'content-type': 'application/json',
                    'accept': 'application/json'
                },
                body: data
            });

            const response = await request.json();

            return response;
        };

        fillButtons.forEach( el => el.addEventListener('click', () => fillFormFields(el)));
        updateButton.addEventListener('click', async e => {
            e.preventDefault();

            let data = getData();
            data.id = updateButton.dataset.id;
            data = JSON.stringify(data);

            disableForm(form);
            showLoading(updateButton);

            const response = await updateUser(data);

            removeMessage();
            enableForm(form);
            hideLoading(updateButton, 'block');

            updateButton.insertAdjacentHTML("beforebegin", response.message);
        });

        /*=== update user status ===*/

        const selectStatus = document.querySelectorAll(".update-user-status");
        
        selectStatus.forEach(el => el.addEventListener('change', () => {
            const currentStatus = el.value;

            updateStatus(el, currentStatus);
        }));
        

    
    </script>
<?php
    $v->end();
?>