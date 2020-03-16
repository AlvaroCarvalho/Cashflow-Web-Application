<?php
    $v->layout("_app");

    $today = new DateTime('now');
    $today = $today->format('Y-m-d');       
?>

<div class = 'new-entry'>
    <h2>Novo lançamento</h2>
    <div class = 'input-container' id = "shift" >
            Turno atual
        <input type = "text" placeholder = "Turno atual" required/>
    </div>
    <div class = 'input-container' id= "next-day">
        Esse turno foi aberto no dia anterior deste lançamento. Deseja adiantar para próximo dia?
        <input type="checkbox">Sim
    </div>
    <div class = 'input-container' id = "number" >
        Nº do pedido
        <input type = "text" placeholder = "Número do pedido" required/>
    </div>
    <form class = 'new-entry__content'>
        <label class = 'input-container' id = "action">
            Tipo de operação
            <select required>
                <option disabled selected value="">Selecione...</option>
                <?php
                    if (isset($actions) && !empty($actions)) :
                        foreach ($actions as $action) :
                ?>
                    <option value = "<?= $action->id; ?>"><?= $action->name; ?></option>
                <?php
                        endforeach;
                    endif;
                ?>
            </select>
        </label>
        <label class = 'input-container' id = 'payment-methods'>
            Forma de pagamento
            <select required>
                <option disabled selected value="">Selecione...</option>
                <?php
                    if (isset($payments) && !empty($payments)) :
                        foreach ($payments as $payment) :
                ?>
                    <option data-action-id = "<?= $payment->action_id; ?>" value="<?= $payment->id; ?>"><?= $payment->name; ?></option>
                <?php
                        endforeach;
                    endif;
                ?>
            </select>
        </label>
        <label class = 'input-container' id = "amount">
            Valor
            <input type = "number" min="0" required/>
        </label>
        <label class = 'input-container'>
            <button id = "add-entry">Adicionar</button>
        </label>
    </form>
</div>

<div class = 'entries'>
    <h2>Lançamentos</h2>
    <form class = 'entries__list'>
        <ul class = 'list__item list__header'>
            <li>Turno</li>
            <li>Adiantamento</li>
            <li>Pagamento</li>
            <li>Operação</li>
            <li>Valor</li>
            <li>Apagar</li>
        </ul>
        <div class = 'input-container' id = 'total-container'>
            <input id = 'total-amount' readonly = "true"></input>
        </div>
        <div class = 'input-container'>
            <textarea placeholder="Observações" id="comments"></textarea>
        </div>
        <div class = 'input-container'>
            <button id="create-entry" data-action="<?= url('app/lancamentos/create'); ?>">Salvar</button>
        </div>
    </form>
    <div class = 'input-container' id='operations'>
        <a href="<?= url("app/operacoes"); ?>"><button>Operações</button></a>
    </div>
    <p id="shift-total"></p>
</div>

<?php
    $v->start("script");
?>
<script>
    /*=== Filter payment methods select field ===*/

    const paymentMethodsSelect = document.querySelector("#payment-methods select");
    const paymentMethodsOptions = paymentMethodsSelect.querySelectorAll("option");
    let paymentMethods = Array.from(paymentMethodsOptions);
    
    const action = document.querySelector("#action select");

    const filterPaymentMethods = id => {
        let filteredPaymentMethods = paymentMethods.filter(el => el.dataset.actionId == id);

        paymentMethodsSelect.innerHTML = '';
        filteredPaymentMethods.forEach(el => paymentMethodsSelect.appendChild(el)); 
    };

    action.addEventListener("change", () => {
        filterPaymentMethods(action.value);
    });

    /*=== Create new payment ===*/

    const addEntryButton = document.getElementById("add-entry");
    const totalContainer = document.getElementById("total-container");
    const newEntryform = document.querySelector(".new-entry__content");

    const createNewEntry = () => {

        let data = {
            shift: document.querySelector("#shift input").value,
            number: document.querySelector("#number input").value,
            nextDay: document.querySelector("#next-day input").checked,
            paymentId: paymentMethodsSelect.value,
            paymentName: paymentMethodsSelect.options[paymentMethodsSelect.selectedIndex].text,
            actionId: action.value,
            actionName: action.options[action.selectedIndex].text,
            amount: document.querySelector("#amount input").value
        };

        for (let key in data) {
            if (key != 'nextDay' && !data[key]) {
                return false;
            }
        }

        const html = `
        <ul class = 'list__item' data-shift='${data.shift}' data-number='${data.number}' data-next-day='${data.nextDay}' data-payment-id='${data.paymentId}' data-action-id='${data.actionId}' data-amount='${data.amount}'>
            <li>${data.number}</li>
            <li>${data.nextDay ? 'Sim' : 'Não'}</li>
            <li>${data.paymentName}</li>
            <li>${data.actionName}</li>
            <li>R$ ${parseFloat(data.amount).toFixed(2).replace('.', ',')}</li>
            <li><i class = 'icon ion-md-trash remove-income'></i></li>
        </ul>`;

        return html;
    };

    /*=== Remove entry ===*/

    const removeIncomes = () => {
        const removeIncomeButtons = document.querySelectorAll(".remove-income");
        
        removeIncomeButtons.forEach( el => {
            if (!el.getAttribute("listener")) {
                el.setAttribute("listener", true);
                el.addEventListener("click", () => {
                    const entry = el.parentNode.parentNode;
                    entry.parentNode.removeChild(entry);

                    const totalAmount = calculateTotalAmount();
                    document.getElementById("total-amount").value = `Total: R$ ${totalAmount.toFixed(2).replace('.', ',')}`;
                    document.getElementById("total-amount").setAttribute("data-total-amount", totalAmount);
                });
            }
        });
    };

    //====================================

    const calculateTotalAmount = () => {

        const entries = document.querySelectorAll(".entries__list ul");
        let totalAmount = 0;

        entries.forEach( (el, index) => {
            if (index) {
                totalAmount += parseFloat(el.dataset.amount);
            }
        })

        return totalAmount;
    }

    addEntryButton.addEventListener('click', e => {
        e.preventDefault();
        let firstIncome = window.localStorage.getItem("firstIncome");

        if (!firstIncome) {
            const today = new Date();
            window.localStorage.setItem("firstIncome", today.getDate());
            addToEntryList();
        } else {
            const nextDayInput = document.getElementById("next-day");
            firstIncome = parseInt(firstIncome);
            const today = new Date();
            if (firstIncome < today.getDate() && !(nextDayInput.style.display == "block")) {
                nextDayInput.style.display = "block";
            } else {
                addToEntryList();
                document.getElementById("action").focus();
            }
        }        
    });

    const addToEntryList = () => {
        let html = createNewEntry();
        const message = document.querySelectorAll("#message");

        if (html) {
            if (message.length) {
                message.forEach(el => el.parentNode.removeChild(el));
            }

            totalContainer.insertAdjacentHTML("beforebegin", html);
            newEntryform.reset();

            paymentMethodsSelect.innerHTML = '';
            paymentMethodsOptions.forEach(el => paymentMethodsSelect.appendChild(el));
            paymentMethodsSelect.value = "";

            
            const totalAmount = calculateTotalAmount();

            document.getElementById("total-amount").value = `Total: R$ ${totalAmount.toFixed(2).replace('.', ',')}`;
            document.getElementById("total-amount").setAttribute("data-total-amount", totalAmount);
            document.getElementById("total-container").style.display = 'block';

            removeIncomes();
        } else {
            html = "<p class = 'error' id = 'message'>Preencha todos os campos!</p>"; 

            if (message.length) {
                message.forEach(el => el.parentNode.removeChild(el));
            }
            
            addEntryButton.insertAdjacentHTML("beforeBegin", html);
        }
    };

    /*=== Save entry ===*/

    const getEntriesData = () => {
        const entries = document.querySelectorAll(".entries__list ul");
        const total = parseFloat(document.getElementById("total-amount").dataset.totalAmount);
        const comments = document.getElementById("comments").value;
        const entriesData = [];
        const data = [];

        entries.forEach( (el, index) => {
            if (index) {
                const entry = {
                    shift: el.dataset.shift,
                    number: el.dataset.number,
                    nextDay: JSON.parse(el.dataset.nextDay.toLowerCase()),
                    paymentId: el.dataset.paymentId,
                    actionId: el.dataset.actionId,
                    amount: el.dataset.amount
                };

                data.push(entry);
            }
        });

        entriesData.push(data);
        entriesData.push(total);
        entriesData.push(comments);

        return entriesData;
    };

    const createEntryRequest = async data => {
        const url = document.getElementById("create-entry").dataset.action;

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

    const renderTotalStored = () => {
        const amount = window.localStorage.getItem("amount");
        const value = amount ? parseFloat(amount) : '';

        if (!isNaN(value) && value)
            document.getElementById("shift-total").innerHTML = `Fechamento Atual: R$ ${value.toFixed(2).replace('.', ',')}`;
    }

    renderTotalStored();

    const triggerCreateEntry = async () => {
        let value = parseFloat(document.getElementById("total-amount").dataset.totalAmount);
        let newTotal = 0;
        let currentTotal = window.localStorage.getItem("amount");

        if (currentTotal) {
            newTotal = parseFloat(currentTotal) + value;
        } else {
            newTotal = value;
        }

        window.localStorage.setItem("amount", newTotal);   
        currentTotal = window.localStorage.getItem("amount");

        let data = getEntriesData();

        if (data[1]) {
            data = JSON.stringify(data);
            const request = await createEntryRequest(data);
            
            if (request.success) { 
                renderTotalStored();
                document.getElementById("total-container").style.display = 'block';
                document.querySelector("#number input").value = '';
                document.getElementById("comments").value = '';
                document.querySelector("#shift input").focus();

                const entries = document.querySelectorAll(".entries__list ul");
                const total = document.getElementById("total-amount");
                const totalContainer = document.getElementById("total-container");

                entries.forEach( (el, index) => {
                    if (index) {
                        el.parentNode.removeChild(el);
                    }
                });

                total.value = "";
                total.dataset.totalAmount = "0";
            }

            totalContainer.insertAdjacentHTML("beforeBegin", request.message);
        } 
    };

    document.getElementById("create-entry").addEventListener("click", e => {
        e.preventDefault();
        triggerCreateEntry();       
    });

    //========== Persist Shift in Local Storage ==========//

    const shift = document.querySelector('#shift input');
    const currentShift = window.localStorage.getItem('shift');

    shift.addEventListener('change', () => {
        window.localStorage.setItem('shift', shift.value);
    });

    if (currentShift)
        shift.value = currentShift;

    //========== Navigation ==========//

    const shiftInput = document.querySelector("#shift input");
    const numberInput = document.querySelector('#number input');
    const actionInput = document.querySelector('#action select');
    const paymentInput = document.querySelector('#payment-methods select');
    const amountInput = document.querySelector('#amount input');

    const addFocusListener = (el, target) => {
        el.addEventListener('keydown', e => {
            if (e.keyCode === 13) {
                e.preventDefault();
                target.focus();
            }
        });
    };

    addFocusListener(shiftInput, numberInput);
    addFocusListener(numberInput, actionInput);
    addFocusListener(actionInput, paymentInput);
    addFocusListener(paymentInput, amountInput);
    addFocusListener(amountInput, addEntryButton);

    const createEntryButton = document.getElementById("create-entry");

    document.addEventListener("keydown", e => {
        if (e.keyCode === 112) {
            e.preventDefault();
            triggerCreateEntry();
        }
    });

    //========== Get order on changing the order number ==========//

    const renderOrderHistory = (number, data) => {
        let html = `<div class='entries__list history'><h2>Histórico do pedido: ${number}</h2><ul class = 'list__item list__header'>
            <li>Turno</li>
            <li>Adiantamento</li>
            <li>Pagamento</li>
            <li>Operação</li>
            <li>Valor</li>
        </ul>`;

        data.forEach(el => {
            html += `<ul class='list__item'>
                        <li>${el.shift}</li>
                        <li>${el.next_day == "0" ? "Não" : "Sim"}</li>
                        <li>${el.payment}</li>
                        <li>${el.action}</li>
                        <li>R$ ${el.amount.replace(".", ",")}</li>
                    </ul>
            `
        });

        document.querySelector(".entries__list").insertAdjacentHTML("afterend", html);        
    };

    const orderNumberInput = document.querySelector("#number input");

    orderNumberInput.addEventListener("change", async function(){
        const order = await fetch(`${baseURL}/lancamentos/get/${this.value}`);
        const data = await order.json();

        if (data.success && data.incomes.length) {
            renderOrderHistory(this.value, data.incomes);
        }
    })

</script>
<?php
    $v->end();
?>