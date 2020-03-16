<?php
    $v->layout("_app");
?>
<div class = 'reports'>
    <h2>Relatórios</h2>
    <form class = 'reports__filter'>
        <label class = 'input-container'>
            De
            <input type = "date" id = "filter__from"/>
        </label>
        <label class = 'input-container'>
            Até
            <input type = "date" id = "filter__to"/>
        </label>
        <label class = 'input-container'>
            <button id = "get-report" data-action = "<?= url("app/relatorios/reports"); ?>">Gerar Relatório</button>
        </label>
    </form>
    <div class="reports__totals">
        <ul class='total-per-payment'>
            <li class="title">Total</li>
        </ul>
    </div>
    <div class = 'reports__table'>
        <ul class='shift'>
            <li class="title">Turno</li>
        </ul>
        <ul class='order'>
            <li class="title">Nº Pedido</li>
        </ul>
        <ul class='date'>
            <li class="title">Data</li>
        </ul>
        <ul class='total'>
            <li class="title">Recebimentos</li>
        </ul>
        <?php
            if (isset($payments) && !empty($payments)) :
                foreach ($payments as $payment) :
        ?>
                    <ul data-id="<?= $payment->id; ?>" data-name="<?= $payment->name; ?>"class = 'payment-methods'>
                        <li class="title"><?= $payment->name; ?></li>
                    </ul>
        <?php
                endforeach;
            endif;
        ?>
    </div>
</div>

<?php
    $v->start("script");
?>
    <script>
        
        /*=== DOM Elements ===*/        
        
        const getReportButton = document.getElementById("get-report");

        /*=== Request Report ===*/

        const getReport = async url => {
            const filterFrom = document.getElementById("filter__from").value;
            const filterTo = document.getElementById("filter__to").value;

            const filter = {
                from: filterFrom,
                to: filterTo
            };
            
            const queryString = JSON.stringify(filter);
            const request = await fetch(`${url}/${queryString}`);
            const report = await request.json();
            
            return report;
        };

        //========================

        /*=== Render Report ===*/

        const clearTable = () => {
            const shiftList = document.querySelector(".shift");
            const ordersList = document.querySelector(".order");
            const dateList = document.querySelector(".date");
            const totalList = document.querySelector(".total");
            const paymentMethodList = document.querySelectorAll(".payment-methods");
            const totalPerPayment = document.querySelector(".total-per-payment");
            
            totalPerPayment.innerHTML = '<li class="title">Total</li>';
            shiftList.innerHTML = '<li class="title">Turno</li>';
            ordersList.innerHTML = '<li class="title">Nº Pedido</li>';
            dateList.innerHTML = '<li class="title">Data</li>';
            totalList.innerHTML = '<li class="title">Recebimentos</li>';
            paymentMethodList.forEach(el => el.innerHTML = `<li class="title">${el.dataset.name}</li>`);
        };

        const renderIncomesTable = reports => {
            const shiftList = document.querySelector(".shift");
            const ordersList = document.querySelector(".order");
            const dateList = document.querySelector(".date");
            const totalList = document.querySelector(".total");
            const paymentMethodList = document.querySelectorAll(".payment-methods");
            const totals = document.querySelector(".reports__totals");

            clearTable();

            reports.forEach(async el => {
                let date = new Date(el.date.split(' ')[0]);
                let formattedDate = [];
                let shift = el.shift;
                formattedDate.push(date.getUTCDate());
                formattedDate.push(date.getUTCMonth() + 1);

                let shiftElement = `<li>${shift}</li>`;
                let order = `<li>${el.number}</li>`; 
                let dateElement = `<li>${formattedDate.join("/")}</li>`;
                let total = `<li>${parseFloat(el.total).toFixed(2).replace('.', ',')}</li>`;

                let paymentMethods = JSON.parse(el.payment);
                let amounts = JSON.parse(el.amount);

                paymentMethodList.forEach(el => {
                    const id = el.dataset.id;
                    let html = '<li class="empty">00</li>';;

                    paymentMethods.forEach((p,index) => {
                        if (p == id) {
                            html = `<li>${parseFloat(amounts[index]).toFixed(2).replace('.', ',')}</li>`; 
                        }
                    });

                    el.insertAdjacentHTML("beforeend", html);
                });
            
                shiftList.insertAdjacentHTML("beforeend", shiftElement);
                ordersList.insertAdjacentHTML("beforeend", order);
                dateList.insertAdjacentHTML("beforeend", dateElement);
                totalList.insertAdjacentHTML("beforeend", total);
            });
        };

        const clearTotal = () => {
            const totals = document.querySelector(".reports__totals");
            totals.innerHTML = "<ul class='total-per-payment'><li class='title'>Total</li></ul>";
        };

        const renderTotalPerPayment = reports => {
            clearTotal();

            const total = {};
            const nextDay = {};
            let nextDayTotal = 0;
            const paymentMethodList = document.querySelectorAll(".payment-methods");
            const totalList = document.querySelector(".total-per-payment");

            paymentMethodList.forEach(el => {
                total[el.dataset.id] = 0;
                nextDay[el.dataset.id] = 0;
            });

            total.total = 0;

            reports.forEach(el => {
                let isForNextDay = el.next_day == true;
                let paymentMethods = JSON.parse(el.payment);
                let amounts = JSON.parse(el.amount);

                paymentMethods.forEach((p, i) => {
                    if (isForNextDay) {
                        nextDay[p] += parseFloat(amounts[i]);
                        nextDayTotal += parseFloat(amounts[i]);
                    } else {
                        total[p] += parseFloat(amounts[i]);
                        total.total += parseFloat(amounts[i]);
                    }
                });
            });
            
            let nextDayContainer = `<ul class='next-day'><li class='title'>Dia Seguinte</li><li>${nextDayTotal.toFixed(2).replace('.', ',')}</li></li>`;

            for (value in nextDay){
                nextDayContainer += `<li>${nextDay[value].toFixed(2).replace('.', ',')}</li>`;
            }

            nextDayContainer += `</ul>`;

            document.querySelector('.reports__totals').insertAdjacentHTML('afterbegin', nextDayContainer);
            
            totalList.insertAdjacentHTML("beforeend", `<li data-value='${total.total}'>${total.total.toFixed(2).replace('.', ',')}</li>`);

            for (value in total) {
                let html;

                if (value !== "total") {
                    if (value == 0) {
                        html = '<li class = "empty">00</li>';
                    } else {
                        html = `<li data-value='${total[value].toFixed(2)}'>${total[value].toFixed(2).replace('.', ',')}</li>`;
                    }
                    
                    totalList.insertAdjacentHTML("beforeend", html);
                }
            }
        };

        const renderTotalPerShift = reports => {
            const total = {};
            const paymentMethodList = document.querySelectorAll(".payment-methods");
            const reportsContainer = document.querySelector(".reports__totals");

            reports.forEach(el => {
                if (!total[el.shift]) {
                    total[el.shift] = {};
                }
            });

            for (shift in total) {
                paymentMethodList.forEach(el => total[shift][el.dataset.id] = 0);
                total[shift]["total"] = 0;

                reports.forEach(el => {
                    const payments = JSON.parse(el.payment);
                    const amounts = JSON.parse(el.amount);

                    if (el.shift == shift) {
                        total[shift]["total"] += parseFloat(el.total);

                        payments.forEach((p, i) => {
                            total[shift][p] += parseFloat(amounts[i]);
                        });
                    }
                });
            }

            let html;

            for (shift in total) {
                html = `<ul class='total-per-shift'><li class='title'>Turno ${shift}</li>`;
                html += `<li>${total[shift]["total"].toFixed(2).replace('.', ',')}</li>`;
                for (payment in total[shift]) {
                    if (payment != "total") {
                        if (total[shift][payment] == 0) {
                            html += `<li class='empty'>00</li>`;
                        } else {
                            html += `<li>${total[shift][payment].toFixed(2).replace('.', ',')}</li>`;
                        }
                    }
                }
                html += '</ul>';
                reportsContainer.insertAdjacentHTML("afterbegin", html);
            }
        };

        const renderExtraFields = () => {
            const paymentMethodList = document.querySelectorAll(".payment-methods");
            let extras = `<ul class='extras'><li class='title'>Recebimentos Extras</li><li><input type='number' step='0.01' min='0' placeholder='0,00'/></li>`;
            let audit = `<ul class='audit'><li class='title'>Auditoria</li><li><input type='number' step='0.01' min='0' placeholder='0,00'/></li>`;
            let credit = `<ul class='credit'><li class='title'>Pendura crédito</li><li><input type='number' step='0.01' min='0' placeholder='0,00'/></li>`;

            paymentMethodList.forEach((el, i) => {
                extras += `<li><input type='number' step='0.01' min='0' placeholder='0,00'/></li>`;
                audit += `<li><input type='number' step='0.01' min='0' placeholder='0,00'/></li>`;
                credit += `<li><input type='number' step='0.01' min='0' placeholder = '0,00' /></li>`;
            });
            extras += `</ul>`;
            audit += `</ul>`;
            credit += `</ul>`;

            let html = audit + extras + credit;

            document.querySelector('.total-per-payment').insertAdjacentHTML('beforebegin', html);
        };

        const calculateFields = index => {
            const extras = document.querySelector(".extras");
            const extrasInputs = Array.from(document.querySelectorAll(".extras li"));
            const totalPerPayment = document.querySelectorAll(".total-per-payment li");
            const dayBefore = document.querySelectorAll(".day-before li");
            const diff = document.querySelectorAll(".diff li");
            const audit = document.querySelectorAll(".audit li");
            const credit = document.querySelectorAll(".credit li");
            const sum = document.querySelectorAll(".sum-total li");

            const total = totalPerPayment[index].dataset.value ? parseFloat(totalPerPayment[index].dataset.value) : 0;
            const extra = extrasInputs[index].querySelector('input').value ? parseFloat(extrasInputs[index].querySelector('input').value) : 0;
            const dayBeforeValue = dayBefore[index].dataset.value ? parseFloat(dayBefore[index].dataset.value) : 0;
            const creditValue = credit[index].querySelector('input').value ? parseFloat(credit[index].querySelector('input').value) : 0;
            const result = total + extra + dayBeforeValue + creditValue;
                    
            sum[index].innerHTML = `${result.toFixed(2).replace('.', ',')}`;
            sum[index].setAttribute('data-value', result);

            const sumValue = sum[index].dataset.value ? parseFloat(sum[index].dataset.value) : 0;
            const auditValue = audit[index].querySelector('input').value ? parseFloat(audit[index].querySelector('input').value) : 0;

            let diffValue = auditValue - sumValue;
            const parsedValue = diffValue.toFixed(2).replace('.', ',');

            diff[index].classList.remove('positive');
            diff[index].classList.remove('negative');

            if (diffValue < 0) {
                diff[index].classList.add('negative');
            } else if (diffValue > 0) {
                diff[index].classList.add('positive');
            }

            diff[index].innerHTML = parsedValue;
            diff[index].setAttribute('data-value', parsedValue); 
        };

        const addExtraListener = () => {
            const extrasInputs = Array.from(document.querySelectorAll(".extras li"));
            const creditInputs = document.querySelectorAll(".credit li");

            extrasInputs.forEach((el, i) => {
                el.addEventListener('change', () => calculateFields(i));
            });

            creditInputs.forEach((el, i) => {
                el.addEventListener('change', () => calculateFields(i));
            });
        };

        const auditListener = () => {
            const audit = document.querySelectorAll('.audit li');
            const sumPayment = document.querySelectorAll(".sum-total li");
            const diff = document.querySelectorAll('.diff li');

            audit.forEach((el, i) => {
                if (i) {
                    el.addEventListener('change', () => {
                        const auditValue = el.querySelector('input').value ? parseFloat(el.querySelector('input').value) : 0;
                        const sumValue = sumPayment[i].dataset.value ? parseFloat(sumPayment[i].dataset.value) : 0;

                        const diffValue = auditValue - sumValue;

                        diff[i].classList.remove('positive');
                        diff[i].classList.remove('negative');
                        let cssClass;

                        if (diffValue > 0) {
                            cssClass = 'positive'; 
                            diff[i].classList.add(cssClass);
                        } else if (diffValue < 0) {
                            cssClass = 'negative';
                            diff[i].classList.add(cssClass);
                        }

                        diff[i].setAttribute('data-value', diffValue);
                        diff[i].innerHTML = diffValue.toFixed(2).replace('.', ',');
                    });
                }
            });
        }

        const renderDiff = () => {
            let diff = `<ul class='diff'><li class='title'>Diferença</li>`; 
            const totalPerPayment = document.querySelectorAll(".total-per-payment li");
            const sumPayment = Array.from(document.querySelectorAll(".sum-total li"));
            const audit = Array.from(document.querySelectorAll(".audit li"));

            totalPerPayment.forEach((el, i) => {
                if (i) {
                    let value = parseFloat(audit[i].querySelector('input').value - sumPayment[i].dataset.value);
                    let cssClass;

                    if (value > 0) {
                        cssClass = 'positive'; 
                    } else if (value < 0) {
                        cssClass = 'negative';
                    }

                    diff += `<li class='${cssClass}'data-value='${value}'>${value.toFixed(2).replace('.', ',')}</li>`;
                }
            });

            diff += `</ul>`;

            document.querySelector(".audit").insertAdjacentHTML('beforebegin', diff);
        };

        const renderSumPayment = () => {
            const totalPerPayment = document.querySelectorAll(".total-per-payment li");
            const extras = document.querySelector(".extras");
            const audit = document.querySelector(".audit");
            const dayBefore = document.querySelector(".day-before");
            let total = '<ul class="sum-total"><li class="title">Soma Pagamento</li>';

            totalPerPayment.forEach((el, index) => {
                if (index) {
                    let totalValue = parseFloat(el.dataset.value);
                    let extraValue = extras.childNodes[index].dataset.value ? extras.childNodes[index].dataset.value : 0;
                    let auditValue = audit.childNodes[index].dataset.value ? audit.childNodes[index].dataset.value : 0;
                    let dayBeforeValue = dayBefore.childNodes[index].dataset.value ? parseFloat(dayBefore.childNodes[index].dataset.value) : 0;

                    let sum = extraValue + auditValue + totalValue + dayBeforeValue;

                    total += `<li data-value='${sum}'>${sum.toFixed(2).replace('.', ',')}</li>`;  
                }        
            });

            total += '</ul>';
            dayBefore.insertAdjacentHTML('beforebegin', total);
        };

        const renderStatus = () => {
            const totalPerPayment = document.querySelectorAll(".total-per-payment li");            
            let status = `<ul class='status'><li class='title'>Status</li>`;

            totalPerPayment.forEach((el, i) => {
                if (i) {
                    status += `<li>
                                <select>
                                    <option value="0">Fech</option>
                                    <option value="1">Cx Ok</option>
                                    <option value="2">Rec Ok</option>
                                </select>
                            </li>`;
                }
            });

            status += `</ul>`;
            document.querySelector('.next-day').insertAdjacentHTML('beforebegin', status);
        };

        const renderDayBefore = data => {
            const paymentMethodList = document.querySelectorAll(".payment-methods");
            const methods = {};

            paymentMethodList.forEach(el => {
                methods[el.dataset.id] = 0;
            });

            data.forEach(el => {
                const payment = JSON.parse(el.payment);
                const amount = JSON.parse(el.amount);

                payment.forEach((p,i) => {
                    methods[p] += parseFloat(amount[i]);
                });
            });

            let dayBefore = `<ul class='day-before'><li class='title'>Dia anterior</li><li data-value='0.00'>0,00</li>`;

            for (p in methods) {
                dayBefore += `<li data-value='${methods[p]}'>${methods[p].toFixed(2).replace('.', ',')}</li>`; 
            }

            dayBefore += `</ul>`;

            document.querySelector('.extras').insertAdjacentHTML('beforebegin', dayBefore);
        };

        //========== Render Balances, Bleedings and Comments ==========//

        let DOM = () => {
            return {
                mainContentContainer: document.querySelector('.main-content'),
                oficialInputs: document.querySelectorAll('.closing .oficial'),
                diffInputs: document.querySelectorAll('.closing .diff'),
                userInputs: document.querySelectorAll('.closing .user')      
            }
        };

        const renderClosing = data => {
            const { mainContentContainer } = DOM();

            let closings = mainContentContainer.querySelectorAll('.closing');

            if (closings) {
                closings.forEach(el => el.parentNode.removeChild(el));
            }

            data.forEach(el => {
                let closingContainer = `
                    <div class='closing'>
                        <h2>Fechamento: Turno ${el.shift}</h2>  
                        <div class = 'input-container'>
                            Fechamento
                            <input class='user' type='number' readonly='true' value=${el.closing} />
                        </div> 
                        <div class = 'input-container'>
                            Oficial
                            <input class='oficial' type='number' min='0' step='0.01' />
                        </div>  
                        <div class = 'input-container'>
                            Diferença
                            <input class='diff' type='number' readonly='true' />
                        </div>   
                        <div class = 'input-container'>
                            Sangria
                            <input class='user' type='number' readonly='true' value=${el.bleeding} />
                        </div>
                        <div class = 'input-container'>
                            Oficial
                            <input class='oficial' type='number' min='0' step='0.01'/>
                        </div>  
                        <div class = 'input-container'>
                            Diferença
                            <input class='diff' type='number' readonly='true' />
                        </div>
                        <div class = 'input-container'>
                            Abertura
                            <input class='user' type='number' readonly='true' value=${el.opening} />
                        </div>
                        <div class = 'input-container'>
                            Oficial
                            <input class='oficial' type='number' min='0' step='0.01'/>
                        </div>  
                        <div class = 'input-container'>
                            Diferença
                            <input class='diff' type='number' readonly='true' />
                        </div> 
                        <div class = 'input-container'>
                            Suprimento
                            <input class='user' type='number' readonly='true' value=${el.supply} />
                        </div>
                        <div class = 'input-container'>
                            Oficial
                            <input class='oficial' type='number' min='0' step='0.01'/>
                        </div>  
                        <div class = 'input-container'>
                            Diferença
                            <input class='diff' type='number' readonly='true' />
                        </div>                         
                        <div class = 'input-container'>
                            Pend crédito
                            <input class='user' type='number' readonly='true' value=${el.credit} />
                        </div>
                        <div class = 'input-container'>
                            Oficial
                            <input class='oficial' type='number' min='0' step='0.01'/>
                        </div>  
                        <div class = 'input-container'>
                            Diferença
                            <input class='diff' type='number' readonly='true' />
                        </div>
                        <div class = 'input-container'>
                            Recebimentos
                            <input class='user' type='number' readonly='true' value=${el.total} />
                        </div>
                        <div class = 'input-container'>
                            Oficial
                            <input class='oficial' type='number' min='0' step='0.01'/>
                        </div>  
                        <div class = 'input-container'>
                            Diferença
                            <input class='diff' type='number' readonly='true' />
                        </div>                         
                        <div class = 'input-container'>
                            Saldo
                            <input class='user' type='number' readonly='true' />
                        </div>
                        <div class = 'input-container'>
                            Oficial
                            <input class='oficial' type='number' min='0' step='0.01'/>
                        </div>  
                        <div class = 'input-container'>
                            Diferença
                            <input class='diff' type='number' readonly='true' />
                        </div>                         
                        <div class = 'input-container'>
                            Fita
                            <input class='user' type='number' readonly='true' />
                        </div>
                        <div class = 'input-container'>
                            Oficial
                            <input class='oficial' type='number' min='0' step='0.01'/>
                        </div>  
                        <div class = 'input-container'>
                            Diferença
                            <input class='diff' type='number' readonly='true' />
                        </div>                         
                        <div class = 'input-container'>
                            Diferença
                            <input class='user' type='number' readonly='true' />
                        </div>
                        <div class = 'input-container'>
                            Oficial
                            <input class='oficial' type='number' min='0' step='0.01'/>
                        </div>  
                        <div class = 'input-container'>
                            Diferença
                            <input class='diff' type='number' readonly='true' />
                        </div>                         
                `;
                
                mainContentContainer.insertAdjacentHTML('beforeend', closingContainer);
            });
        };

        const addClosingListener = () => {
            const { userInputs, oficialInputs, diffInputs } = DOM();

            oficialInputs.forEach((el, i) => {
                el.addEventListener('change', () => {
                    const user = userInputs[i].value ? parseFloat(userInputs[i].value) : 0;
                    const oficial = el.value ? parseFloat(el.value) : 0;
                    
                    const result = (oficial - user).toFixed(2);
                    let classCss;

                    diffInputs[i].classList.remove('negative');
                    diffInputs[i].classList.remove('positive');

                    if (result > 0) {
                        diffInputs[i].classList.add('positive');
                    } else if (result < 0) {
                        diffInputs[i].classList.add('negative');
                    }

                    diffInputs[i].value = result;
                });
            });
        };


        /*=== Create Report ===*/

        getReportButton.addEventListener("click", async e => {
            e.preventDefault();
            const report = await getReport(getReportButton.dataset.action);

            if (report.success) {
                renderIncomesTable(report.data.incomes);
                renderTotalPerPayment(report.data.incomes);
                renderTotalPerShift(report.data.incomes);
                renderExtraFields();
                renderDayBefore(report.data.before);
                renderSumPayment();
                renderDiff();
                addExtraListener();
                auditListener();
                renderStatus();
                renderClosing(report.data.balance);
                addClosingListener();
            }
        });

        //========================  

    </script>
<?php
    $v->end();
?>