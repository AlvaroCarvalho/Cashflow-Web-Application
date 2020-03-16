<?php
    $v->layout("_app");
?>

<div class = 'registers-forms'>
    <form class = 'registers-forms__action' method = 'POST' id = 'action-form' >
        <h2>Adicionar tipo</h2>
        <label class = 'input-container'>
            Operação
            <input type = 'text' name = 'action' id = 'action'/>
        </label>
        <label class = 'input-container'>
            <img class = 'loading'src = '<?= url("Source/assets/img/load.gif"); ?>'>
            <input type = 'submit' value = 'Adicionar' id = 'create-action'/>
            <input type = 'submit' value = 'Atualizar' id = 'update-action'/>
        </label>
    </form>
    <form class = 'registers-forms__payment' method = 'POST' id = 'payment-form' >
        <h2>Adicionar pagamento</h2>
        <label class = 'input-container'>
            Pagamento
            <input type = 'text' name = 'payment' id = 'payment'/>
        </label>
        <label class = 'input-container'>
            Vinculação
            <select id = 'related-action'>
                <option selected disabled value="0">Selecione...</option>
                <?php 
                    if (isset($actions) && !empty($actions)) :
                        foreach ($actions as $action) :
                            if ($action->status) :
                ?>
                <option value = '<?= $action->id; ?>'><?= $action->name; ?></option>
                <?php
                            endif;
                        endforeach;
                    endif;
                ?>
            </select>
        </label>
        <label class = 'input-container'>
            Ordem de apresentação
            <input type = 'number' id = 'position' min='0'/>
        </label>
        <label class = 'input-container'>
            Soma no fechamento?
            <input type = 'checkbox' id='sum-checkbox'/>
        </label>
        <label class = 'input-container'>
            <img class = 'loading'src = '<?= url("Source/assets/img/load.gif"); ?>'>
            <input type = 'submit' value = 'Adicionar' id = 'create-payment'/>
            <input type = 'submit' value = 'Atualizar' id = 'update-payment'/>
        </label>
    </form>
</div>
<div class = 'registers__list'>
    <ul class = 'list__tabs'>
        <li class = 'active' id = 'payments-button'>Pagamentos</li>
        <li id  = "actions-button">Tipo</li>
        <li></li>
    </ul>
    <div class = 'list__payment'>
        <div class = 'list-content'>
            <ul class = 'list-content__summary'>
                <li>ID</li>
                <li>Pagamento</li>
                <li>Operação</li>
                <li>Posição</li>
                <li>Status</li>
                <li>Editar</li>
                <li>Apagar</li>
            </ul>
            <?php
                if (isset($payments) && !empty($payments)) :
                    foreach ($payments as $payment) :
            ?>
            <ul class = 'list-content__iten'>
                <li><?= $payment->id; ?></li>
                <li><?= $payment->name; ?></li>
                <li><?= $payment->action_name; ?></li>
                <li><?= $payment->position; ?></li>
                <li>
                    <select data-action = "<?= url("app/cadastros/$payment->id/update_payment_status")?>" class = "update-payment-status">
                        <option value = "1" <?= $payment->status ? "selected" : ""; ?>>Ativo</option>
                        <option value = "0" <?= $payment->status ? "" : "selected"; ?>>Inativo</option>
                    </select>
                </li>
                <li><i data-action = "<?= url('app/cadastros/update_payment') ?>" data-id = "<?= $payment->id; ?>" data-name = "<?= $payment->name; ?>" data-action-id = "<?= $payment->action_id; ?>" class="update-payment icon ion-md-create"></i></li>
                <li><i data-action="<?= url('app/cadastros/destroy_payment/' . $payment->id); ?>" class="destroy-payment icon ion-md-trash"></i></li>
            </ul>
            <?php
                    endforeach;
                else:
            ?>
                <p class = 'no-records'>Não existem pagamentos cadastrados até o momento.</p>
            <?php
                endif;
            ?>
        </div>
    </div>
    <div class = 'list__action'>
        <div class = 'list-content'>
            <ul class = 'list-content__summary'>
                <li>ID</li>
                <li>Operação</li>
                <li>Status</li>
                <li>Editar</li>
                <li>Apagar</li>
            </ul>
            <?php 
                if (isset($actions) && !empty($actions)) :
                    foreach ($actions as $action) :
            ?>
            <ul class = 'list-content__iten'>
                <li><?= $action->id; ?></li>
                <li><?= $action->name; ?></li>
                <li>
                    <select data-action = "<?= url("app/cadastros/$action->id/update_action_status")?>" class = "update-action-status">
                        <option value = "1" <?= $action->status ? "selected" : ""; ?>>Ativo</option>
                        <option value = "0" <?= $action->status ? "" : "selected"; ?>>Inativo</option>
                    </select>
                </li>
                <li><i data-action = "<?= url('app/cadastros/update_action') ?>" data-id = "<?= $action->id; ?>" data-name = "<?= $action->name; ?>" class="update-action icon ion-md-create"></i></li>
                <li><i data-action="<?= url('app/cadastros/destroy_action/' . $action->id); ?>" class="destroy-action icon ion-md-trash"></i></li>
            </ul>
            <?php
                    endforeach;
                else:
            ?>
                <p class = 'no-records'>Não existem operações cadastradas até o momento.</p>
            <?php
                endif;
            ?>
        </div>
    </div>
</div>

<?php 
    $v->start("script");
?>
    <script>
        /*=== Animations ===*/

        const tabs = document.querySelectorAll(".list__tabs li");

        tabs.forEach(el => {
            el.addEventListener("click", function(){
                document.querySelector(".active").classList.remove("active");
                this.classList.add("active");
            });
        });

        const payments = document.querySelector(".list__payment");
        const actions = document.querySelector(".list__action");
        const paymentsButton = document.getElementById("payments-button");
        const actionsButton = document.getElementById("actions-button");

        paymentsButton.addEventListener("click", () => {
            actions.style.display = 'none';
            payments.style.display = 'block';
        });

        actionsButton.addEventListener("click", () => {
            payments.style.display = 'none';
            actions.style.display = 'block';
        });

        /*=== New action request ===*/

        const createActionButton = document.getElementById("create-action");
        const actionForm = document.getElementById("action-form");

        const createAction = async data => {
            
            const request = await fetch(`${baseURL}/cadastros/create_action`, {
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

        createActionButton.addEventListener("click", async e => {
            e.preventDefault();
            const actionName = document.getElementById("action").value;

            showLoading(createActionButton);
            disableForm(actionForm);

            let data = {
                name: actionName
            };

            data = JSON.stringify(data);

            const response = await createAction(data);

            hideLoading(createActionButton, "block");
            enableForm(actionForm);
            removeMessage();

            createActionButton.insertAdjacentHTML("beforebegin", response.message);
        });

        /*=== New payment request ===*/

        const createPaymentButton = document.getElementById("create-payment");
        const paymentForm = document.getElementById("payment-form");

        const createPayment = async data => {
            
            const request = await fetch(`${baseURL}/cadastros/create_payment`, {
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

        createPaymentButton.addEventListener("click", async e => {
            e.preventDefault();
            const checkbox = document.getElementById("sum-checkbox").checked;
            
            let data = {
                name: document.getElementById('payment').value,
                action_id: document.getElementById('related-action').value,
                position: document.getElementById('position').value,
                add_to_report: checkbox ? 1 : 0
            };

            disableForm(paymentForm);
            showLoading(createPaymentButton);

            data = JSON.stringify(data);

            const response = await createPayment(data);

            hideLoading(createPaymentButton, "block");
            enableForm(paymentForm);
            removeMessage();

            createPaymentButton.insertAdjacentHTML("beforebegin", response.message);
        });

        /*=== Destroy Action ===*/

        const deleteActionButton = document.querySelectorAll(".destroy-action");

        const deleteAction = async el => {
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

        deleteActionButton.forEach(el => el.addEventListener('click', async () => {
            
            const response = await deleteAction(el);

            if (response.success) {
                const action = el.parentElement.parentElement;

                action.classList.add("fadeOut");
                setTimeout(() => action.parentElement.removeChild(action), 500);                
            }
        }));

        /*=== Destroy Payment ===*/

        const deletePaymentButton = document.querySelectorAll(".destroy-payment");

        const deletePayment = async el => {
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

        deletePaymentButton.forEach(el => el.addEventListener('click', async () => {
            
            const response = await deletePayment(el);

            if (response.success) {
                const payment = el.parentElement.parentElement;

                payment.classList.add("fadeOut");
                setTimeout(() => payment.parentElement.removeChild(payment), 500);                
            }
        }));

        /*=== Update Action ===*/

        const fillActionForm = document.querySelectorAll(".update-action");
        const updateActionButton = document.getElementById("update-action");

        const fillActionFormFields = el => {
            document.getElementById("action").value = el.dataset.name;
            document.getElementById("create-action").style.display = 'none';
            updateActionButton.style.display = 'block';
            updateActionButton.setAttribute("data-action", el.dataset.action);
            updateActionButton.setAttribute("data-id", el.dataset.id);
        };

        const updateAction = async data => {
            const url = updateActionButton.dataset.action;

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

        fillActionForm.forEach( el => el.addEventListener('click', () => fillActionFormFields(el)));
        updateActionButton.addEventListener('click', async e => {
            e.preventDefault();

            let data = {
                name: document.getElementById("action").value
            };
        
            data.id = updateActionButton.dataset.id;
            data = JSON.stringify(data);

            disableForm(actionForm);
            showLoading(updateActionButton);

            const response = await updateAction(data);

            removeMessage();
            enableForm(actionForm);
            hideLoading(createActionButton, 'block');

            createActionButton.insertAdjacentHTML("beforebegin", response.message);
        });

        /*=== Update Payment ===*/

        const fillPaymentForm = document.querySelectorAll(".update-payment");
        const updatePaymentButton = document.getElementById("update-payment");

        const fillPaymentFormFields = el => {
            document.getElementById("payment").value = el.dataset.name;
            document.getElementById("create-payment").style.display = 'none';
            updatePaymentButton.style.display = 'block';
            updatePaymentButton.setAttribute("data-action", el.dataset.action);
            updatePaymentButton.setAttribute("data-id", el.dataset.id);
        };

        const updatePayment = async data => {
            const url = updatePaymentButton.dataset.action;

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

        fillPaymentForm.forEach( el => el.addEventListener('click', () => fillPaymentFormFields(el)));
        updatePaymentButton.addEventListener('click', async e => {
            e.preventDefault();
            const checkbox = document.getElementById("sum-checkbox").checked;

            let data = {
                name: document.getElementById("payment").value,
                action_id: document.getElementById("related-action").value,
                position: document.getElementById("position").value,
                add_to_report: checkbox ? 1 : 0
            };
        
            data.id = updatePaymentButton.dataset.id;
            data = JSON.stringify(data);

            disableForm(paymentForm);
            showLoading(updatePaymentButton);

            const response = await updatePayment(data);

            removeMessage();
            enableForm(paymentForm);
            hideLoading(createPaymentButton, 'block');

            createPaymentButton.insertAdjacentHTML("beforebegin", response.message);
        });

        /*=== Update Action Status ===*/

        const selectActionStatus = document.querySelectorAll(".update-action-status");
        
        selectActionStatus.forEach(el => el.addEventListener('change', () => {
            const currentStatus = el.value;

            updateStatus(el, currentStatus);
        }));

        /*=== Update Payment Status ===*/

        const selectPaymentStatus = document.querySelectorAll(".update-payment-status");
        
        selectPaymentStatus.forEach(el => el.addEventListener('change', () => {
            const currentStatus = el.value;

            updateStatus(el, currentStatus);
        }));

    </script>
<?php
    $v->end();
?>