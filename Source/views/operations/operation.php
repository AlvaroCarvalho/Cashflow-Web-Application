<?php
    $v->layout("_app");
?>
<section class='outcomes'>
    <div class = 'input-container'>
        Turno
        <input type='number' placeholder='Turno' id='shift' min='1'/>
    </div>
    <div class = 'incomes'>
        <form class="balance">
            <h2>Caixa</h2>
            <div class='input-container'>
                Abertura
                <input type='number' id='opening' placeholder='Abertura' min="0"/>
            </div>
            <div class='input-container'>
                Suprimento
                <input type='number' id='supply' placeholder='Suprimento' min="0"/>
            </div>
            <div class='input-container'>
                Pendura crédito
                <input type='number' id='credit' placeholder='Pendura Crédito' min="0"/>
            </div>
            <div class='input-container'>
                Sangria
                <input type='number' id='bleeding-balance' placeholder='Sangria' min="0"/>
            </div>
            <div class='input-container'>
                Fechamento
                <input type='number' id='closing' placeholder='Fechamento' min="0"/>
            </div>
            <div class='input-container'>
            </div>
            <div class='input-container'>
            </div>
            <div class='input-container'>
                <button id='create-balance' data-action='<?= url('app/operacoes/save_balance')?>'>Fechar</button>
            </div>
        </form>
    </div>
    <div class='input-container'>
        <button id='back' data-action='<?= url('app/lancamentos')?>'>Lançamentos</button>
    </div>
</section>
<section class="operations">
    <div class="bleedings">
        <h2>Sangria</h2>
        <div class='form'>
            <div class='input-container'>
                R$ 0,05
                <input type='number' id='five_cents' placeholder='R$ 0,05' min="0"/>
                <input type='text' id='five_cents_comment' placeholder='Observações'/>
            </div>
            <div class='input-container'>
                R$ 0,10
                <input type='number' id='ten_cents' placeholder='R$ 0,10' min="0"/>
                <input type='text' id='ten_cents_comment' placeholder='Observações'/>
            </div>
            <div class='input-container'>
                R$ 0,25
                <input type='number' id='twenty_five_cents' placeholder='R$ 0,25' min="0"/>
                <input type='text' id='twenty_five_cents_comment' placeholder='Observações'/>
            </div>
            <div class='input-container'>
                R$ 0,50
                <input type='number' id='fifty_cents' placeholder='R$ 0,50' min="0"/>
                <input type='text' id='fifty_cents_comment' placeholder='Observações'/>
            </div>
            <div class='input-container'>
                R$ 1,00
                <input type='number' id='um' placeholder='R$ 1,00' min="0"/>
                <input type='text' id='um_comment' placeholder='Observações'/>
            </div>
            <div class='input-container'>
                R$ 2,00
                <input type='number' id='two' placeholder='R$ 2,00' min="0"/>
                <input type='text' id='two_comment' placeholder='Observações'/>
            </div>
            <div class='input-container'>
                R$ 5,00
                <input type='number' id='five' placeholder='R$ 5,00' min="0"/>
                <input type='text' id='five_comment' placeholder='Observações'/>
            </div>
            <div class='input-container'>
                R$ 10,00
                <input type='number' id='ten' placeholder='R$ 10,00' min="0"/>
                <input type='text' id='ten_comment' placeholder='Observações'/>
            </div>
            <div class='input-container'>
                R$ 20,00
                <input type='number' id='twenty' placeholder='R$ 20,00' min="0"/>
                <input type='text' id='twenty_comment' placeholder='Observações'/>
            </div>
            <div class='input-container'>
                R$ 50,00
                <input type='number' id='fifty' placeholder='R$ 50,00' min="0"/>
                <input type='text' id='fifty_comment' placeholder='Observações'/>
            </div>
            <div class='input-container'>
                R$ 100,00
                <input type='number' id='one_hundred' placeholder='R$ 100,00' min="0"/>
                <input type='text' id='one_hundred_comment' placeholder='Observações'/>
            </div>
            <div class='input-container'>
                Dia seguinte
                <input type='number' id='next_day' placeholder='Dia Seguinte' min="0"/>
                <input type='text' id='next_day_comment' placeholder='Observações'/>
            </div>
            <div class='input-container'>
                Total
                <input type='number' id='total' placeholder='Total' min="0" readonly='true'/>
                <input type='text' id='total_bleedings_comment' placeholder='Observações'/>
            </div>
            <div class='input-container other'>
                Outros
                <input type='number' class='others' placeholder='Outros' min="0"/>
                <input type='text' class='others_comment' placeholder='Observações'/>
            </div>
            <div class='input-container'>
                <button id='add-field'>+</button>
            </div>
            <div class="input-container">
                <button id="create-bleeding" data-action='<?= url('app/operacoes/save_bleeding'); ?>'>Salvar</button>
            </div>
        </div>
    </div>
</section>

<?php
    $v->start("script");
?>
    <script>
        //========== Informations ==========//

        const values = {
            bleedings: 0
        };

        //========== Request Function ==========//

        const request = async (button, data) => {
            const url = button.dataset.action;

            const request = await fetch(url, {
                method: 'POST',
                headers: {
                    'content-type': 'application/json',
                    'accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const response = await request.json();

            return response;
        };

        const renderRequestResponse = (button, message) => {
            const form = button.parentNode.parentNode;
            const inputContainer = button.parentNode;
            const messages = form.querySelector('#message');

            if (messages) {
                form.removeChild(messages);
            }

            inputContainer.insertAdjacentHTML('beforebegin', message);
        };

        const clearFormAfterRequest = button => {
            const form = button.parentNode.parentNode;

            form.reset();
        };

        //========== DOM Elements ==========//

        const DOM = {
            otherField: document.querySelector('.other'),
            shiftField: document.getElementById('shift'),
            closing: document.getElementById('closing'),
            bleedings: {
                form: document.querySelector('.bleedings .form'),
                balance: document.getElementById('bleeding-balance'),
                total: document.getElementById('total'),
                values: document.querySelectorAll('.bleedings input[type=number]'),
                comments: document.querySelectorAll('.bleedings input[type=text]'),
                button: document.getElementById('create-bleeding'),
                addField: document.getElementById('add-field')
            },
            balance: {
                button: document.getElementById('create-balance'),
                opening: document.getElementById('opening'),
                supply: document.getElementById('supply'),
                closing: document.getElementById('closing'),
                bleeding: document.getElementById('bleeding-balance'),
                credit: document.getElementById('credit')
            },
            backButton: document.getElementById('back')
        };

        //========== Shift and Total Persistence ==========//

        const currentShift = window.localStorage.getItem('shift');

        if (shift)
            DOM.shiftField.value = currentShift;
        
        DOM.shiftField.addEventListener('change', () => {
            window.localStorage.setItem('shift', DOM.shiftField.value);
        });

        const closingAmount = window.localStorage.getItem('amount');

        if (closingAmount && !isNaN(closingAmount))
            DOM.closing.value = parseFloat(closingAmount);

        //========== Add other input ==========//

        DOM.bleedings.addField.addEventListener('click', function(e){
            e.preventDefault();

            const field = DOM.otherField.cloneNode(true);            
            DOM.bleedings.form.insertBefore(field, DOM.bleedings.addField.parentNode);
        });

        //========== Calculate Total of Bleedings ==========//

        const calculateTotalOfBleedings = () => {
            const allBleedings = DOM.bleedings.values;
            let total = 0;

            allBleedings.forEach(el => {
                if (el.getAttribute('id') != 'total') {
                    total += el.value ? parseFloat(el.value) : 0;
                }
            });

            values.bleedings = total;
            DOM.bleedings.balance.value = total;
            DOM.bleedings.total.value = total;
        };
        
        const addBleedingsListener = () => {
            const allBleedings = DOM.bleedings.values;

            allBleedings.forEach( el => el.addEventListener('change', calculateTotalOfBleedings));
        }

        addBleedingsListener();

        //========== Save Balance ==========//

        const getBalanceData = () => {
            let data = {
                shift: DOM.shiftField.value,
                opening: DOM.balance.opening.value,
                supply: DOM.balance.supply.value,
                closing: DOM.balance.closing.value,
                bleeding: DOM.balance.bleeding.value,
                credit: DOM.balance.credit.value
            }

            for (value in data) {
                data[value] = data[value] ? parseFloat(data[value]) : 0;
            }

            return data;
        };

        DOM.balance.button.addEventListener('click', async function(e){
            e.preventDefault();
            let data = getBalanceData();
            let response = await request(this, data);
    
            if (response.success) {
                clearFormAfterRequest(this);
                window.localStorage.removeItem('amount');
            }

            renderRequestResponse(this, response.message);
        });    

        //========== Save Bleedings ==========//

        const getBleedingsData = () => {
            let data = {
                values: {},
                comments: {}
            };

            data.shift = DOM.shiftField.value;

            DOM.bleedings.values.forEach(el => {
                if (el.id)
                    data.values[el.id] = el.value ? parseFloat(el.value) : 0;
            });

            DOM.bleedings.comments.forEach(el => {
                if (el.id) {
                    if (el.id == 'total_bleedings_comment') {
                        data.comments[el.id.replace('_bleedings_comment', '')] = el.value;
                    } else {
                        data.comments[el.id.replace('_comment', '')] = el.value;
                    }
                }
            });

            data.values.others = [];
            data.comments.others = [];

            document.querySelectorAll('.other').forEach(el => {
                const value = el.querySelector('input[type=number]').value;
                const comment = el.querySelector('input[type=text]').value;

                data.values.others.push(value ? parseFloat(value) : 0);
                data.comments.others.push(comment);
            });

            return data;
        }

        DOM.bleedings.button.addEventListener('click', async function(e){
            e.preventDefault();

            const data = getBleedingsData();
            const response = await request(this, data);

            if (response.success)
                clearFormAfterRequest(this);
            
            renderRequestResponse(this, response.message);
        });

        //========== Go back ==========//

        DOM.backButton.addEventListener('click', function(){
            window.location.href = this.dataset.action;
        })

    </script>
<?php
    $v->end();
?>