document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal');
    const freeSiteBtn = document.getElementById('freeSiteBtn');
    const closeBtn = document.querySelector('.close');
    const telegramForm = document.getElementById('telegramForm');
    const notification = document.getElementById('notification');
    const notificationText = document.getElementById('notificationText');

    freeSiteBtn.addEventListener('click', function() {
        modal.style.display = 'block';
    });

    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    telegramForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const usernameInput = document.getElementById('username');
        const username = usernameInput.value.trim();
        const submitBtn = telegramForm.querySelector('.submit-btn');

        if (!username.startsWith('@')) {
            showNotification('Username должен начинаться с @', 'error');
            return;
        }

        if (username.length < 2) {
            showNotification('Введите корректный username', 'error');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Отправка...';

        fetch('send_telegram.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'username=' + encodeURIComponent(username)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Заявка отправлена! Мы свяжемся с вами в Telegram.', 'success');
                telegramForm.reset();
                modal.style.display = 'none';
            } else {
                showNotification('Ошибка отправки: ' + data.error, 'error');
            }
        })
        .catch(error => {
            showNotification('Ошибка сети. Попробуйте еще раз.', 'error');
            console.error('Error:', error);
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Отправить заявку';
        });
    });

    function showNotification(message, type) {
        notificationText.textContent = message;
        notification.className = 'notification ' + type;
        notification.classList.remove('hidden');
        
        setTimeout(() => {
            notification.classList.add('hidden');
        }, 5000);
    }

    document.getElementById('username').addEventListener('input', function(e) {
        const value = e.target.value;
        if (!value.startsWith('@') && value.length > 0) {
            e.target.value = '@' + value.replace('@', '');
        }
    });
});
