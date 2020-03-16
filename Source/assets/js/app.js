const baseURL = 'http://localhost/facilpizza/app'; //https://development.davimanoel.com.br/facilpizza/app';

const removeMessage = () => {
    const element = document.getElementById('message');

    if (element) {
        const parent = element.parentNode;
        parent.removeChild(element);
    }
};

const showLoading = element => {
    element.style.display = 'none';
    const loading = element.parentNode.querySelector(".loading");
    loading.style.display = 'block';
};

const hideLoading = (element, display) => {
    element.style.display = display;
    const loading = element.parentNode.querySelector(".loading");
    loading.style.display = 'none';
};

const disableForm = form => {
    form.setAttribute("readonly", true);
    form.style.opacity = "0.5";
    form.reset();
};

const enableForm = form => {
    form.setAttribute("readonly", false);
    form.style.opacity = "1";
};

const updateStatus = async (select, currentStatus) => {

    const url = `${select.dataset.action}/${currentStatus}`;

    const request = await fetch(url, {
        method: 'PUT',
        headers: {
            'accept': 'application/json'
        }
    });
}