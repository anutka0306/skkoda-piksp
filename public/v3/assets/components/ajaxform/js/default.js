function showToast(message, type = 'success') {

    const toast = document.getElementById('toast');

    toast.textContent = message;
    toast.className = `${type} show`;

    clearTimeout(toast.timer);

    toast.timer = setTimeout(() => {
        toast.classList.remove('show');
    }, 3500);
}

document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('form.ajax_form').forEach(form => {

        form.addEventListener('submit', async function (e) {

            e.preventDefault();

            const btn = form.querySelector('button[type="submit"], button');

            btn.disabled = true;

            const formData = new FormData(form);

            formData.append('url', window.location.href);

            try {

                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {

                    showToast(result.message, 'success');

                    form.reset();
                    if (window.jQuery && $.fancybox) {
                        $.fancybox.close();
                    }

                } else {

                    showToast(result.message, 'success');

                }

            } catch (e) {

                console.error(e);

                showToast('Ошибка отправки формы', 'error');

            } finally {

                btn.disabled = false;

            }

        });

    });

});